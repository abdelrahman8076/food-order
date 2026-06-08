<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusLog;
use App\Models\StoreSetting;
use App\Support\CartHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(private CouponService $couponService)
    {
    }

    public const STATUSES = [
        'pending' => 'Order Placed',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'ready' => 'Ready for Pickup',
        'out_for_delivery' => 'Picked up for Delivery',
        'delivered' => 'Delivered',
    ];

    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['ready', 'cancelled'],
        'ready' => ['out_for_delivery', 'cancelled'],
        'out_for_delivery' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function buildCartItems(array $cart): array
    {
        $items = [];
        $subtotal = 0;
        $cart = CartHelper::normalizeCart($cart);

        foreach ($cart as $id => $entry) {
            $item = Item::find($id);
            if ($item && $item->is_available) {
                $total = $item->price * $entry['quantity'];
                $items[] = (object) [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $entry['quantity'],
                    'notes' => $entry['notes'],
                    'total' => $total,
                ];
                $subtotal += $total;
            }
        }

        return compact('items', 'subtotal');
    }

    public function calculatePricing(float $subtotal, string $orderType, float $discount = 0): array
    {
        $settings = StoreSetting::current();
        $discount = min($discount, $subtotal);
        $discountedSubtotal = max(0, $subtotal - $discount);
        $tax = round($discountedSubtotal * (float) $settings->tax_rate, 2);
        $deliveryFee = $orderType === 'delivery' && $subtotal < (float) $settings->free_delivery_min
            ? (float) $settings->delivery_fee
            : 0;
        $total = $discountedSubtotal + $tax + $deliveryFee;

        return compact('tax', 'deliveryFee', 'total', 'discount', 'discountedSubtotal');
    }

    public function createOrder(array $cart, array $customerData): Order
    {
        $built = $this->buildCartItems($cart);
        if (empty($built['items'])) {
            throw new InvalidArgumentException('No valid items in cart.');
        }

        $orderType = 'delivery';
        $coupon = null;
        $discount = 0;

        if (!empty($customerData['coupon_id'])) {
            $coupon = Coupon::find($customerData['coupon_id']);
            if (!$coupon) {
                throw new InvalidArgumentException('The applied coupon is no longer valid.');
            }
            $applied = $this->couponService->apply($coupon, $built['subtotal']);
            $discount = $applied['discount'];
        }

        $pricing = $this->calculatePricing($built['subtotal'], $orderType, $discount);
        $deliveryAddress = $this->buildDeliveryAddressSnapshot($customerData);

        return DB::transaction(function () use ($cart, $customerData, $built, $pricing, $orderType, $deliveryAddress, $coupon, $discount) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'tracking_token' => Str::random(32),
                'user_id' => auth()->id(),
                'customer_name' => $customerData['customer_name'],
                'customer_phone' => $customerData['customer_phone'],
                'customer_phone_normalized' => Order::normalizePhone($customerData['customer_phone']),
                'customer_email' => $customerData['customer_email'] ?? null,
                'order_type' => $orderType,
                'delivery_address' => $deliveryAddress,
                'delivery_city' => $customerData['delivery_city'] ?? null,
                'delivery_area' => $customerData['delivery_area'] ?? null,
                'delivery_street' => $customerData['delivery_street'] ?? null,
                'delivery_building' => $customerData['delivery_building'] ?? null,
                'delivery_floor' => $customerData['delivery_floor'] ?? null,
                'delivery_apartment' => $customerData['delivery_apartment'] ?? null,
                'payment_method' => $customerData['payment_method'] ?? 'cash',
                'status' => 'pending',
                'subtotal' => $built['subtotal'],
                'tax' => $pricing['tax'],
                'delivery_fee' => $pricing['deliveryFee'],
                'total' => $pricing['total'],
                'notes' => $customerData['notes'] ?? null,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'discount' => $discount,
            ]);

            $cart = CartHelper::normalizeCart($cart);

            foreach ($cart as $id => $entry) {
                $item = Item::find($id);
                if (!$item || !$item->is_available) {
                    continue;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'unit_price' => $item->price,
                    'quantity' => $entry['quantity'],
                    'notes' => $entry['notes'],
                    'total' => $item->price * $entry['quantity'],
                ]);
            }

            $this->logStatus($order, 'pending', 'Order placed');

            if ($coupon) {
                $coupon->increment('used_count');
            }

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $note = null): Order
    {
        $allowed = self::TRANSITIONS[$order->status] ?? [];
        if (!in_array($newStatus, $allowed, true)) {
            throw new InvalidArgumentException("Cannot transition from {$order->status} to {$newStatus}.");
        }

        $order->update(['status' => $newStatus]);
        $this->setTimestamp($order, $newStatus);
        $this->logStatus($order, $newStatus, $note);

        return $order->fresh();
    }

    public function advanceStatus(Order $order): Order
    {
        $flow = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered'];
        $index = array_search($order->status, $flow, true);
        if ($index === false || $index >= count($flow) - 1) {
            throw new InvalidArgumentException('Order cannot be advanced further.');
        }

        return $this->updateStatus($order, $flow[$index + 1]);
    }

    public function getTimeline(Order $order): array
    {
        $logs = $order->statusLogs()->orderBy('created_at')->get()->keyBy('status');
        $current = $order->status;
        $isCancelled = $current === 'cancelled';

        if ($isCancelled) {
            return [
                [
                    'key' => 'cancelled',
                    'label' => 'Cancelled',
                    'state' => 'current',
                    'timestamp' => $logs->get('cancelled')?->created_at ?? $order->updated_at,
                ],
            ];
        }

        $steps = [];
        $passedCurrent = false;

        foreach (self::STATUSES as $key => $label) {
            $timestamp = $this->getStatusTimestamp($order, $key, $logs);
            $state = 'pending';

            if ($key === $current) {
                $state = $current === 'delivered' ? 'completed' : 'current';
                $passedCurrent = true;
            } elseif (!$passedCurrent && $timestamp) {
                $state = 'completed';
            }

            $steps[] = compact('key', 'label', 'state', 'timestamp');
        }

        return $steps;
    }

    public function getCurrentStep(Order $order): int
    {
        $keys = array_keys(self::STATUSES);
        $index = array_search($order->status, $keys, true);

        return $index !== false ? $index : 0;
    }

    public function toStatusJson(Order $order): array
    {
        return [
            'status' => $order->status,
            'status_label' => self::STATUSES[$order->status] ?? ucfirst($order->status),
            'current_step' => $this->getCurrentStep($order),
            'timeline' => collect($this->getTimeline($order))->map(fn ($s) => [
                'key' => $s['key'],
                'label' => $s['label'],
                'state' => $s['state'],
                'timestamp' => $s['timestamp']?->toIso8601String(),
            ]),
        ];
    }

    private function buildDeliveryAddressSnapshot(array $customerData): string
    {
        $parts = array_filter([
            $customerData['delivery_city'] ?? null,
            $customerData['delivery_area'] ?? null,
            $customerData['delivery_street'] ?? null,
            isset($customerData['delivery_building']) ? 'Building ' . $customerData['delivery_building'] : null,
            !empty($customerData['delivery_floor']) ? 'Floor ' . $customerData['delivery_floor'] : null,
            !empty($customerData['delivery_apartment']) ? 'Apt ' . $customerData['delivery_apartment'] : null,
        ]);

        return implode(', ', $parts);
    }

    private function logStatus(Order $order, string $status, ?string $note = null): void
    {
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => $status,
            'note' => $note,
            'created_at' => now(),
        ]);
    }

    private function setTimestamp(Order $order, string $status): void
    {
        $map = [
            'confirmed' => 'confirmed_at',
            'preparing' => 'preparing_at',
            'ready' => 'ready_at',
            'out_for_delivery' => 'out_for_delivery_at',
            'delivered' => 'delivered_at',
        ];

        if (isset($map[$status])) {
            $order->update([$map[$status] => now()]);
        }
    }

    private function getStatusTimestamp(Order $order, string $status, $logs)
    {
        if ($logs->has($status)) {
            return $logs->get($status)->created_at;
        }

        return match ($status) {
            'pending' => $order->created_at,
            'confirmed' => $order->confirmed_at,
            'preparing' => $order->preparing_at,
            'ready' => $order->ready_at,
            'out_for_delivery' => $order->out_for_delivery_at,
            'delivered' => $order->delivered_at,
            default => null,
        };
    }
}
