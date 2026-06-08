<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function deliveredOrdersQuery(Carbon $from, Carbon $to): Builder
    {
        return Order::query()
            ->where('status', 'delivered')
            ->whereBetween('created_at', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ]);
    }

    public function summary(Carbon $from, Carbon $to): array
    {
        $query = $this->deliveredOrdersQuery($from, $to);

        $revenue = (float) (clone $query)->sum('total');
        $orders = (clone $query)->count();
        $customers = (int) (clone $query)
            ->whereNotNull('customer_phone_normalized')
            ->where('customer_phone_normalized', '!=', '')
            ->selectRaw('COUNT(DISTINCT customer_phone_normalized) as count')
            ->value('count');

        $itemsSold = (int) $this->deliveredOrderItemsQuery($from, $to)->sum('order_items.quantity');

        return [
            'revenue' => $revenue,
            'orders' => $orders,
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0,
            'customers' => $customers,
            'items_sold' => $itemsSold,
        ];
    }

    public function revenueByDay(Carbon $from, Carbon $to): Collection
    {
        return $this->deliveredOrdersQuery($from, $to)
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'revenue' => (float) $row->revenue,
                'orders' => (int) $row->orders,
            ]);
    }

    public function topItemsByQuantity(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return $this->deliveredOrderItemsQuery($from, $to)
            ->selectRaw('order_items.item_name as name, SUM(order_items.quantity) as quantity')
            ->groupBy('order_items.item_name')
            ->orderByDesc('quantity')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'quantity' => (int) $row->quantity,
            ]);
    }

    public function topItemsByRevenue(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return $this->deliveredOrderItemsQuery($from, $to)
            ->selectRaw('order_items.item_name as name, SUM(order_items.total) as revenue')
            ->groupBy('order_items.item_name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'revenue' => (float) $row->revenue,
            ]);
    }

    public function ordersByDeliveryArea(Carbon $from, Carbon $to): Collection
    {
        return $this->deliveredOrdersQuery($from, $to)
            ->selectRaw("COALESCE(NULLIF(delivery_area, ''), 'Unknown') as area, COUNT(*) as orders")
            ->groupBy(DB::raw("COALESCE(NULLIF(delivery_area, ''), 'Unknown')"))
            ->orderByDesc('orders')
            ->get()
            ->map(fn ($row) => [
                'area' => $row->area,
                'orders' => (int) $row->orders,
            ]);
    }

    public function revenueByCategory(Carbon $from, Carbon $to): Collection
    {
        return $this->deliveredOrderItemsQuery($from, $to)
            ->leftJoin('items', 'order_items.item_id', '=', 'items.id')
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->selectRaw("COALESCE(categories.name, 'Uncategorized') as category, SUM(order_items.total) as revenue")
            ->groupBy(DB::raw("COALESCE(categories.name, 'Uncategorized')"))
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category,
                'revenue' => (float) $row->revenue,
            ]);
    }

    public function topCustomers(Carbon $from, Carbon $to, int $limit = 10): Collection
    {
        return $this->deliveredOrdersQuery($from, $to)
            ->whereNotNull('customer_phone_normalized')
            ->where('customer_phone_normalized', '!=', '')
            ->selectRaw('customer_name, customer_phone, customer_phone_normalized, COUNT(*) as orders, SUM(total) as spent')
            ->groupBy('customer_phone_normalized', 'customer_name', 'customer_phone')
            ->orderByDesc('spent')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->customer_name,
                'phone' => $row->customer_phone,
                'orders' => (int) $row->orders,
                'spent' => (float) $row->spent,
            ]);
    }

    public function couponStats(Carbon $from, Carbon $to): Collection
    {
        return $this->deliveredOrdersQuery($from, $to)
            ->whereNotNull('coupon_id')
            ->selectRaw('coupon_code as code, COUNT(*) as uses, SUM(discount) as total_discount')
            ->groupBy('coupon_code')
            ->orderByDesc('uses')
            ->get()
            ->map(fn ($row) => [
                'code' => $row->code,
                'uses' => (int) $row->uses,
                'total_discount' => (float) $row->total_discount,
            ]);
    }

    public function monthToDateSummary(): array
    {
        $from = now()->startOfMonth();
        $to = now();
        $summary = $this->summary($from, $to);
        $topItem = $this->topItemsByQuantity($from, $to, 1)->first();

        return array_merge($summary, [
            'top_item_name' => $topItem['name'] ?? null,
            'top_item_quantity' => $topItem['quantity'] ?? 0,
        ]);
    }

    public function resolveDateRange(?string $preset, ?string $fromInput, ?string $toInput): array
    {
        $to = now()->endOfDay();

        $preset = $preset ?: '30d';

        return match ($preset) {
            'today' => [
                'from' => now()->startOfDay(),
                'to' => $to,
                'preset' => 'today',
            ],
            '7d' => [
                'from' => now()->subDays(6)->startOfDay(),
                'to' => $to,
                'preset' => '7d',
            ],
            'month' => [
                'from' => now()->startOfMonth(),
                'to' => $to,
                'preset' => 'month',
            ],
            'custom' => [
                'from' => Carbon::parse($fromInput ?? now()->subDays(29))->startOfDay(),
                'to' => Carbon::parse($toInput ?? now())->endOfDay(),
                'preset' => 'custom',
            ],
            default => [
                'from' => now()->subDays(29)->startOfDay(),
                'to' => $to,
                'preset' => '30d',
            ],
        };
    }

    private function deliveredOrderItemsQuery(Carbon $from, Carbon $to): Builder
    {
        return OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->whereBetween('orders.created_at', [
                $from->copy()->startOfDay(),
                $to->copy()->endOfDay(),
            ]);
    }
}
