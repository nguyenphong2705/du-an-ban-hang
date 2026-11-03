<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_percent',
        'description',
        'min_order_amount',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'integer',
            'min_order_amount' => 'integer',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at->endOfDay()->isPast();
    }
}
