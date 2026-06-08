<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\OrderService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index(Request $request)
    {
        $query = Order::with('orderItems');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        $orders = $query->latest()->paginate(15);

        $boardOrders = $this->boardOrders();

        return view('admin.orders.index', compact('orders', 'boardOrders'));
    }

    public function board()
    {
        $boardOrders = $this->boardOrders();

        return response()->json([
            'html' => view('admin.orders._kitchen-board-columns', compact('boardOrders'))->render(),
        ]);
    }

    private function boardOrders()
    {
        return Order::with('orderItems')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->latest()
            ->get()
            ->groupBy('status');
    }

    public function show(Order $order)
    {
        $order->load(['orderItems', 'statusLogs']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_merge(array_keys(OrderService::STATUSES), ['cancelled'])),
        ]);

        try {
            $this->orderService->updateStatus($order, $validated['status']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Order status updated.');
    }

    public function advance(Order $order)
    {
        try {
            $this->orderService->advanceStatus($order);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Order advanced to next stage.');
    }

    public function settings()
    {
        $settings = StoreSetting::current();

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_phone' => 'nullable|string|max:50',
            'store_address' => 'nullable|string',
            'store_hours' => 'nullable|string|max:255',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'delivery_fee' => 'required|numeric|min:0',
            'free_delivery_min' => 'required|numeric|min:0',
        ]);

        StoreSetting::current()->update($validated);

        return back()->with('success', 'Store settings updated.');
    }
}
