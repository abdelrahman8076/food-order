<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function show(Request $request)
    {
        $order = null;
        if ($request->has('order_number')) {
            $order = Order::where('order_number', $request->get('order_number'))->with('orderItems')->first();
        }
        return view('order.track', compact('order'));
    }

    public function confirmation(Order $order)
    {
        $order->load('orderItems');
        return view('order.confirmation', compact('order'));
    }
}
