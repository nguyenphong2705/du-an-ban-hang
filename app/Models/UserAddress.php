<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model UserAddress (Bảng: user_addresses)
 *
 * Lưu trữ Sổ địa chỉ nhận hàng linh hoạt của khách hàng:
 * - Cho phép khách hàng lưu từ 2, 3 hoặc nhiều địa chỉ nhận hàng (Nhà riêng, Công ty, Người thân...).
 * - Cờ `is_default` xác định địa chỉ nhận hàng mặc định tự động điền khi thanh toán (/thanh-toan).
 */
class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'recipient_phone',
        'street_address',
        'ward',
        'district',
        'province',
        'address',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Liên kết ngược tới tài khoản Khách hàng sở hữu địa chỉ này.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
