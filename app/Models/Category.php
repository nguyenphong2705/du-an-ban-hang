<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
    ];

    public function getImageAttribute(?string $value): string
    {
        if (!$value || str_contains($value, '1608248597359-0e6d526a6c18')) {
            return 'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?auto=format&fit=crop&w=800&q=80';
        }

        return $value;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
