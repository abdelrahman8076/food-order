<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StoreSetting;
use App\Services\OrderService;
use App\Support\CartHelper;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function index()
    {
        $cart = CartHelper::normalizeCart(session()->get('cart', []));
        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $entry) {
            $item = Item::find($id);
            if ($item && $item->is_available) {
                $total = $item->price * $entry['quantity'];
                $items[] = (object) [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'name' => $item->localizedName(),
                    'price' => $item->price,
                    'quantity' => $entry['quantity'],
                    'notes' => $entry['notes'],
                    'total' => $total,
                ];
                $subtotal += $total;
            }
        }

        $settings = StoreSetting::current();
        $pricing = $this->orderService->calculatePricing($subtotal, 'delivery');

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $pricing['tax'],
            'deliveryFee' => $pricing['deliveryFee'],
            'total' => $pricing['total'],
            'settings' => $settings,
        ]);
    }

    public function add(Request $request, Item $item)
    {
        if (!$item->is_available) {
            return back()->with('error', __('messages.item_not_available'));
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:20',
            'notes' => 'nullable|string|max:50',
        ]);

        $cart = CartHelper::normalizeCart(session()->get('cart', []));
        $qty = max(1, (int) ($validated['quantity'] ?? 1));
        $notes = isset($validated['notes']) && trim($validated['notes']) !== ''
            ? trim($validated['notes'])
            : null;

        $existing = $cart[$item->id] ?? ['quantity' => 0, 'notes' => null];
        $cart[$item->id] = [
            'quantity' => $existing['quantity'] + $qty,
            'notes' => $notes ?? $existing['notes'],
        ];

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json([
                'cart_count' => CartHelper::cartCount($cart),
                'message' => __('messages.item_added_to_cart', ['name' => $item->localizedName()]),
            ]);
        }

        return back()->with('success', __('messages.item_added_to_cart', ['name' => $item->localizedName()]));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:50',
        ]);

        $cart = CartHelper::normalizeCart(session()->get('cart', []));
        $id = (int) $validated['item_id'];

        if (!isset($cart[$id])) {
            return back()->with('error', __('messages.item_not_in_cart'));
        }

        $notes = isset($validated['notes']) && trim($validated['notes']) !== ''
            ? trim($validated['notes'])
            : null;

        $cart[$id] = [
            'quantity' => (int) $validated['quantity'],
            'notes' => $notes,
        ];

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json(['cart_count' => CartHelper::cartCount($cart)]);
        }

        return back()->with('success', __('messages.item_updated'));
    }

    public function remove(Request $request, Item $item)
    {
        $cart = session()->get('cart', []);
        unset($cart[$item->id]);
        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json(['cart_count' => CartHelper::cartCount($cart)]);
        }

        return back()->with('success', __('messages.item_removed'));
    }
}
