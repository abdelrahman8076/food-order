<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_phone',
        'store_address',
        'store_hours',
        'tax_rate',
        'delivery_fee',
        'free_delivery_min',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:4',
        'delivery_fee' => 'decimal:2',
        'free_delivery_min' => 'decimal:2',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'store_name' => 'EATSHUB',
            'store_phone' => '',
            'tax_rate' => 0.10,
            'delivery_fee' => 3.99,
            'free_delivery_min' => 25.00,
        ]);
    }
}
