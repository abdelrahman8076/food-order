<?php

namespace App\Support;

class CartHelper
{
    public static function normalizeEntry(mixed $entry): array
    {
        if (is_array($entry)) {
            return [
                'quantity' => max(1, (int) ($entry['quantity'] ?? 1)),
                'notes' => isset($entry['notes']) && $entry['notes'] !== ''
                    ? (string) $entry['notes']
                    : null,
            ];
        }

        return [
            'quantity' => max(1, (int) $entry),
            'notes' => null,
        ];
    }

    public static function normalizeCart(array $cart): array
    {
        $normalized = [];
        foreach ($cart as $id => $entry) {
            $normalized[$id] = self::normalizeEntry($entry);
        }

        return $normalized;
    }

    public static function cartCount(array $cart): int
    {
        return array_sum(array_column(self::normalizeCart($cart), 'quantity'));
    }
}
