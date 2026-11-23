<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'product_cat',
        'brand',
        'brand_id',
        'name',
        'description',
        'price',
        'stock',
        'weight',
        'image',
        'skin_type',
        'ingredients',
        'benefits',
        'how_to_use',
        'warnings',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(
            Category::class,
            'product_cat',
            'cat_id'
        );
    }

    public function brandRelation(): BelongsTo
    {
        return $this->belongsTo(
            Brand::class,
            'brand_id',
            'brand_id'
        );
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
