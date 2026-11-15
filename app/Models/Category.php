<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'cat_title',
        'description',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_cat', 'cat_id');
    }
}