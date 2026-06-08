<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    private const TERMINAL_STATUSES = ['delivered', 'cancelled'];

    public function __construct(private OrderService $orderService)
    {
    }

    public function show(Request $request)
    {
        $order = null;
        $activeOrder = null;
        $orders = collect();
        $searched = false;
        $searchedPhone = null;
        $searchedOrderNumber = null;
        $trackingToken = $request->input('token');

        if ($request->filled('order_number') && $request->filled('token')) {
            $searched = true;
            $searchedOrderNumber = strtoupper(trim($request->input('order_number')));
            $token = $request->input('token');

            $found = Order::where('order_number', $searchedOrderNumber)
                ->with(['orderItems', 'statusLogs'])
                ->first();

            if ($found && $found->canBeViewedBy(auth()->user(), null, $token)) {
                Order::markSessionVerified($found->id);
                $order = $found;
                $trackingToken = $found->tracking_token;
            }
        } elseif ($request->filled('order_number') && auth()->check()) {
            $searched = true;
            $searchedOrderNumber = strtoupper(trim($request->input('order_number')));

            $found = Order::where('order_number', $searchedOrderNumber)
                ->with(['orderItems', 'statusLogs'])
                ->first();

            if ($found && $found->canBeViewedBy(auth()->user(), null, null)) {
                Order::markSessionVerified($found->id);
                $order = $found;
                $trackingToken = $found->tracking_token;
            }
        } elseif ($request->filled('phone')) {
            $searched = true;
            $searchedPhone = trim($request->input('phone'));
            $normalizedPhone = Order::normalizePhone($searchedPhone);

            $orders = Order::where('customer_phone_normalized', $normalizedPhone)
                ->where('created_at', '>=', now()->subDays(15))
                ->with(['orderItems', 'statusLogs'])
                ->latest()
                ->get();

            foreach ($orders as $listedOrder) {
                Order::markSessionVerified($listedOrder->id);
            }

            $activeOrder = $orders->first(
                fn (Order $listedOrder) => !in_array($listedOrder->status, self::TERMINAL_STATUSES, true)
            );

            if ($request->filled('order_number')) {
                $searchedOrderNumber = strtoupper(trim($request->input('order_number')));
                $order = $orders->firstWhere('order_number', $searchedOrderNumber) ?? $activeOrder;
            } else {
                $order = $activeOrder ?? $orders->first();
            }

            if ($order) {
                $trackingToken = $order->tracking_token;
            }
        }

        return view('order.track', compact(
            'order',
            'activeOrder',
            'orders',
            'searched',
            'searchedPhone',
            'searchedOrderNumber',
            'trackingToken',
        ));
    }

    public function confirmation(Request $request, Order $order)
    {
        $token = $request->query('token');

        if (!$order->canBeViewedBy(auth()->user(), null, $token)) {
            return redirect()->route('order.track', ['phone' => $order->customer_phone])
                ->with('error', __('messages.verify_phone'));
        }

        Order::markSessionVerified($order->id);

        $order->load(['orderItems', 'statusLogs']);
        $settings = \App\Models\StoreSetting::current();

        return view('order.confirmation', compact('order', 'settings'));
    }

    public function status(Request $request, Order $order)
    {
        $token = $request->query('token');

        if (!$order->canBeViewedBy(auth()->user(), null, $token)) {
            abort(403);
        }

        $order->load('statusLogs');

        return response()->json($this->orderService->toStatusJson($order));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('orderItems')
            ->latest()
            ->paginate(10);

        foreach ($orders as $listedOrder) {
            Order::markSessionVerified($listedOrder->id);
        }

        return view('order.my-orders', compact('orders'));
    }
}
