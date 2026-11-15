<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'pending',
        'processing',
        'shipped',
        'completed',
        'cancelled',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid',
        'paid',
        'failed',
        'expired',
        'refunded',
    ];

    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'address_detail',
        'delivery_note',

        // Informasi pengiriman
        'shipping_cost',
        'shipping_weight',
        'shipping_courier',
        'shipping_service',
        'shipping_etd',

        // Informasi pesanan dan pembayaran
        'total',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'paid_at',
        'snap_token',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'shipping_cost' => 'integer',
        'shipping_weight' => 'integer',
        'paid_at' => 'datetime',
        'stock_deducted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
