<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('orderItems');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
        ]);
        $order->update(['status' => $validated['status']]);
        if ($validated['status'] === 'confirmed') {
            $order->update(['confirmed_at' => now()]);
        }
        if ($validated['status'] === 'ready') {
            $order->update(['ready_at' => now()]);
        }
        return back()->with('success', 'Order status updated.');
    }
}
