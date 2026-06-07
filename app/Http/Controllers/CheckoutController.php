<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }
        $items = [];
        $subtotal = 0;
        foreach ($cart as $id => $qty) {
            $item = Item::find($id);
            if ($item && $item->is_available) {
                $items[] = (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $qty,
                    'total' => $item->price * $qty,
                ];
                $subtotal += $item->price * $qty;
            }
        }
        $taxRate = 0.10; // 10%
        $tax = round($subtotal * $taxRate, 2);
        $deliveryFee = $subtotal < 25 ? 3.99 : 0;
        $total = $subtotal + $tax + $deliveryFee;
        return view('checkout.index', compact('items', 'subtotal', 'tax', 'deliveryFee', 'total'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email',
            'order_type' => 'required|in:delivery,pickup,dine_in',
            'delivery_address' => 'required_if:order_type,delivery|nullable|string',
            'table_number' => 'required_if:order_type,dine_in|nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = 0;
        $orderItemsData = [];
        foreach ($cart as $id => $qty) {
            $item = Item::find($id);
            if (!$item || !$item->is_available) continue;
            $lineTotal = $item->price * $qty;
            $subtotal += $lineTotal;
            $orderItemsData[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'unit_price' => $item->price,
                'quantity' => $qty,
                'total' => $lineTotal,
            ];
        }
        if (empty($orderItemsData)) {
            return redirect()->route('menu.index')->with('error', 'No valid items in cart.');
        }

        $taxRate = 0.10;
        $tax = round($subtotal * $taxRate, 2);
        $deliveryFee = $validated['order_type'] === 'delivery' && $subtotal < 25 ? 3.99 : 0;
        $total = $subtotal + $tax + $deliveryFee;

        $order = DB::transaction(function () use ($validated, $subtotal, $tax, $deliveryFee, $total, $orderItemsData) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'order_type' => $validated['order_type'],
                'delivery_address' => $validated['delivery_address'] ?? null,
                'table_number' => $validated['table_number'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);
            foreach ($orderItemsData as $row) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $row['item_id'],
                    'item_name' => $row['item_name'],
                    'unit_price' => $row['unit_price'],
                    'quantity' => $row['quantity'],
                    'total' => $row['total'],
                ]);
            }
            return $order;
        });

        session()->forget('cart');
        return redirect()->route('order.confirmation', $order->id)
            ->with('success', 'Order placed successfully. Your order number is ' . $order->order_number);
    }
}
