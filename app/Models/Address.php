<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'user_id',
    'label',
    'recipient_name',
    'phone',
    'province_code',
    'province_name',
    'city_code',
    'city_name',
    'district_code',
    'district_name',
    'village_code',
    'village_name',
    'postal_code',
    'detail',
    'is_default',
])]
class Address extends Model
{
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
