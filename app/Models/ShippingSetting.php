<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'store_name',
        'province_name',
        'province_code',
        'city_name',
        'city_code',
        'district_name',
        'district_code',
        'rajaongkir_origin_id',
        'address',
        'postal_code',
    ];
}
