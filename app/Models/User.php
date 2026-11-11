<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'phone',
        'address',
        'password',
        'is_admin',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class)->orderByDesc('is_default')->orderBy('id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class)->latest();
    }

    public function wishlistProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    /**
     * Đảm bảo bảng user_addresses và wishlists tồn tại trong DB và khởi tạo dữ liệu mẫu.
     */
    public static function ensureCustomerFeaturesSchema(): void
    {
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('label')->default('Nhà riêng');
                $table->string('recipient_name')->nullable();
                $table->string('recipient_phone', 30)->nullable();
                $table->string('street_address', 300)->nullable();
                $table->string('ward', 150)->nullable();
                $table->string('district', 150)->nullable();
                $table->string('province', 150)->nullable();
                $table->string('address', 500);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'product_id']);
            });
        }
    }

    /**
     * Khởi tạo sổ địa chỉ cho tài khoản nếu chưa có địa chỉ nào được lưu.
     */
    public function ensureAddressesInitialized(): void
    {
        if ($this->addresses()->exists()) {
            return;
        }

        if ($this->username === 'tuyenthanhthanh3979') {
            $sampleAddresses = [
                [
                    'label' => 'Nhà riêng',
                    'recipient_name' => $this->name,
                    'recipient_phone' => $this->phone ?: '0909397979',
                    'street_address' => '215 Lê Thánh Tôn',
                    'ward' => 'Phường Bến Thành',
                    'district' => 'Quận 1',
                    'province' => 'TP. Hồ Chí Minh',
                    'address' => '215 Lê Thánh Tôn, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh',
                    'is_default' => true,
                ],
                [
                    'label' => 'Văn phòng Công ty',
                    'recipient_name' => $this->name,
                    'recipient_phone' => $this->phone ?: '0909397979',
                    'street_address' => 'Tòa nhà Bitexco, Số 2 Hải Triều',
                    'ward' => 'Phường Bến Nghé',
                    'district' => 'Quận 1',
                    'province' => 'TP. Hồ Chí Minh',
                    'address' => 'Tòa nhà Bitexco, Số 2 Hải Triều, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
                    'is_default' => false,
                ],
                [
                    'label' => 'Địa chỉ người thân',
                    'recipient_name' => $this->name,
                    'recipient_phone' => $this->phone ?: '0909397979',
                    'street_address' => 'Số 28 Phạm Văn Chiêu',
                    'ward' => 'Phường 14',
                    'district' => 'Quận Gò Vấp',
                    'province' => 'TP. Hồ Chí Minh',
                    'address' => 'Số 28 Phạm Văn Chiêu, Phường 14, Quận Gò Vấp, TP. Hồ Chí Minh',
                    'is_default' => false,
                ],
            ];

            foreach ($sampleAddresses as $addr) {
                $this->addresses()->create($addr);
            }
            return;
        }

        if (!empty($this->address)) {
            $parts = array_map('trim', explode(',', $this->address));
            $count = count($parts);
            $province = $count >= 1 ? $parts[$count - 1] : 'TP. Hồ Chí Minh';
            $district = $count >= 2 ? $parts[$count - 2] : 'Quận 1';
            $ward = $count >= 3 ? $parts[$count - 3] : 'Phường Bến Thành';
            $street = $count >= 4 ? implode(', ', array_slice($parts, 0, $count - 3)) : $this->address;

            $this->addresses()->create([
                'label' => 'Địa chỉ mặc định',
                'recipient_name' => $this->name,
                'recipient_phone' => $this->phone,
                'street_address' => $street,
                'ward' => $ward,
                'district' => $district,
                'province' => $province,
                'address' => $this->address,
                'is_default' => true,
            ]);
        }
    }
}
