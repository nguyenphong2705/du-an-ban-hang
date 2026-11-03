<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'category_id',
        'brand_id',
        'name',
        'slug',
        'price',
        'sale_price',
        'stock',
        'images',
        'description',
        'ingredients',
        'skin_type',
        'volume',
        'is_best_seller',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_best_seller' => 'boolean',
            'price' => 'integer',
            'sale_price' => 'integer',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function getFinalPriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function getProductCodeAttribute(): string
    {
        return $this->sku ?: ('LMN-SP' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT));
    }

    public function getFirstImageAttribute(): string
    {
        $imgs = $this->images;
        if (is_array($imgs) && count($imgs) > 0) {
            return $imgs[0];
        }
        return 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=900&q=80';
    }
}
