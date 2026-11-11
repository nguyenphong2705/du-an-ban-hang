<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Wishlist (Bảng: wishlists)
 *
 * Lưu trữ danh sách sản phẩm Yêu Thích (thả tim ❤️) của khách hàng đã đăng nhập:
 * - Mỗi bản ghi liên kết (user_id, product_id) duy nhất.
 * - Hiển thị tại trang riêng `/tai-khoan/yeu-thich` và làm sáng nút trái tim trên toàn website.
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Khách hàng đã thả tim yêu thích sản phẩm.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sản phẩm mỹ phẩm được yêu thích.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
