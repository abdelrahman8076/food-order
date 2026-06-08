<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'tracking_token',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_phone_normalized',
        'customer_email',
        'delivery_address',
        'delivery_city',
        'delivery_area',
        'delivery_street',
        'delivery_building',
        'delivery_floor',
        'delivery_apartment',
        'table_number',
        'order_type',
        'payment_method',
        'status',
        'subtotal',
        'tax',
        'delivery_fee',
        'total',
        'notes',
        'coupon_id',
        'coupon_code',
        'discount',
        'confirmed_at',
        'preparing_at',
        'ready_at',
        'out_for_delivery_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())->count() + 1;
        return $prefix . $date . str_pad((string) $last, 4, '0', STR_PAD_LEFT);
    }

    public function formattedDeliveryAddress(): string
    {
        if ($this->delivery_city) {
            $parts = array_filter([
                $this->delivery_city,
                $this->delivery_area,
                $this->delivery_street,
                $this->delivery_building ? 'Building ' . $this->delivery_building : null,
                $this->delivery_floor ? 'Floor ' . $this->delivery_floor : null,
                $this->delivery_apartment ? 'Apt ' . $this->delivery_apartment : null,
            ]);

            return implode(', ', $parts);
        }

        return $this->delivery_address ?? '';
    }

    public static function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public function canBeViewedBy(?User $user, ?string $phone, ?string $token): bool
    {
        if ($user && $this->user_id === $user->id) {
            return true;
        }

        if (static::isSessionVerified($this->id)) {
            return true;
        }

        if ($token && $this->tracking_token && hash_equals($this->tracking_token, $token)) {
            return true;
        }

        if ($phone && static::normalizePhone($phone) === static::normalizePhone($this->customer_phone)) {
            return true;
        }

        return false;
    }

    public static function markSessionVerified(int $orderId): void
    {
        $verified = session()->get('verified_order_ids', []);
        if (!in_array($orderId, $verified, true)) {
            $verified[] = $orderId;
            session()->put('verified_order_ids', $verified);
        }
    }

    public static function isSessionVerified(int $orderId): bool
    {
        return in_array($orderId, session()->get('verified_order_ids', []), true);
    }
}
