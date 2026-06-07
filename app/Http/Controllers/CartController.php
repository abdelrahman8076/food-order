<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
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
        return view('cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request, Item $item)
    {
        if (!$item->is_available) {
            return back()->with('error', 'This item is not available.');
        }
        $cart = session()->get('cart', []);
        $qty = max(1, (int) $request->get('quantity', 1));
        $cart[$item->id] = ($cart[$item->id] ?? 0) + $qty;
        session()->put('cart', $cart);
        if ($request->wantsJson()) {
            return response()->json(['cart_count' => array_sum($cart), 'message' => 'Added to cart']);
        }
        return back()->with('success', $item->name . ' added to cart.');
    }

    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        $id = (int) $request->get('item_id');
        $qty = max(0, (int) $request->get('quantity', 0));
        if ($qty === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $qty;
        }
        session()->put('cart', $cart);
        if ($request->wantsJson()) {
            return response()->json(['cart_count' => array_sum($cart)]);
        }
        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        unset($cart[$item->id]);
        session()->put('cart', $cart);
        if ($request->wantsJson()) {
            return response()->json(['cart_count' => array_sum($cart)]);
        }
        return back()->with('success', 'Item removed from cart.');
    }
}
