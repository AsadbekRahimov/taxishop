<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'order_type',
        'driver_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'payment_method',
        'status',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function isPickup(): bool
    {
        return $this->order_type === 'pickup';
    }

    public function isDelivery(): bool
    {
        return $this->order_type === 'delivery';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $last = Order::max('id') ?? 0;
                $order->order_number = 'TS-' . str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
