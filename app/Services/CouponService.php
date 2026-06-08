<?php

namespace App\Services;

use App\Models\Coupon;
use InvalidArgumentException;

class CouponService
{
    public function findByCode(string $code): ?Coupon
    {
        $normalized = strtoupper(trim($code));
        if ($normalized === '') {
            return null;
        }

        return Coupon::where('code', $normalized)->first();
    }

    public function validate(Coupon $coupon, float $subtotal): void
    {
        $message = $coupon->validationMessage($subtotal);
        if ($message !== null) {
            throw new InvalidArgumentException($message);
        }
    }

    public function apply(Coupon $coupon, float $subtotal): array
    {
        $this->validate($coupon, $subtotal);

        return [
            'discount' => $coupon->calculateDiscount($subtotal),
            'coupon' => $coupon,
        ];
    }
}
