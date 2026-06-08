<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function calculateDiscount(float $subtotal): float
    {
        $discount = $this->type === 'percent'
            ? round($subtotal * (float) $this->value / 100, 2)
            : min((float) $this->value, $subtotal);

        return min($discount, $subtotal);
    }

    public function isUsable(float $subtotal): bool
    {
        return $this->validationMessage($subtotal) === null;
    }

    public function validationMessage(float $subtotal): ?string
    {
        if (!$this->is_active) {
            return __('orders.coupon_inactive');
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return __('orders.coupon_expired');
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return __('orders.coupon_limit_reached');
        }

        if ($this->min_order_amount !== null && $subtotal < (float) $this->min_order_amount) {
            return __('orders.coupon_min_order', [
                'amount' => number_format((float) $this->min_order_amount, 2),
            ]);
        }

        return null;
    }

    public function formattedValue(): string
    {
        return $this->type === 'percent'
            ? rtrim(rtrim(number_format((float) $this->value, 2), '0'), '.') . '%'
            : '$' . number_format((float) $this->value, 2);
    }
}
