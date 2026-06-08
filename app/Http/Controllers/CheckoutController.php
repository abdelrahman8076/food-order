<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Coupon;
use App\Models\DeliveryArea;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Services\CouponService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CouponService $couponService,
    ) {
    }

    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }

        $built = $this->orderService->buildCartItems($cart);
        $settings = StoreSetting::current();
        $appliedCoupon = $this->resolveAppliedCoupon($built['subtotal']);
        $discount = $appliedCoupon ? $appliedCoupon->calculateDiscount($built['subtotal']) : 0;
        $pricing = $this->orderService->calculatePricing($built['subtotal'], 'delivery', $discount);
        $cities = City::active()->with('activeAreas')->orderBy('name')->get();

        return view('checkout.index', [
            'items' => $built['items'],
            'subtotal' => $built['subtotal'],
            'tax' => $pricing['tax'],
            'deliveryFee' => $pricing['deliveryFee'],
            'discount' => $pricing['discount'],
            'total' => $pricing['total'],
            'settings' => $settings,
            'cities' => $cities,
            'appliedCoupon' => $appliedCoupon,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }

        $built = $this->orderService->buildCartItems($cart);
        $coupon = $this->couponService->findByCode($request->input('code'));

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        try {
            $this->couponService->apply($coupon, $built['subtotal']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->put('checkout_coupon_id', $coupon->id);

        return back()->with('success', 'Coupon ' . $coupon->code . ' applied.');
    }

    public function removeCoupon()
    {
        session()->forget('checkout_coupon_id');

        return back()->with('success', 'Coupon removed.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:delivery_areas,id',
            'delivery_street' => 'required|string|max:255',
            'delivery_building' => 'required|string|max:100',
            'delivery_floor' => 'nullable|string|max:50',
            'delivery_apartment' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $city = City::active()->find($validated['city_id']);
        $area = DeliveryArea::active()
            ->where('city_id', $validated['city_id'])
            ->find($validated['area_id']);

        if (!$city || !$area) {
            throw ValidationException::withMessages([
                'area_id' => 'Please select a valid city and area.',
            ]);
        }

        $validated['order_type'] = 'delivery';
        $validated['delivery_city'] = $city->name;
        $validated['delivery_area'] = $area->name;

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('menu.index')->with('error', 'Your cart is empty.');
        }

        if ($couponId = session('checkout_coupon_id')) {
            $validated['coupon_id'] = $couponId;
        }

        try {
            $order = $this->orderService->createOrder($cart, $validated);
        } catch (InvalidArgumentException $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }

        session()->forget('cart');
        session()->forget('checkout_coupon_id');

        Order::markSessionVerified($order->id);

        return redirect()->route('order.confirmation', [
            'order' => $order->id,
            'token' => $order->tracking_token,
        ])->with('success', 'Order placed! Your order number is ' . $order->order_number);
    }

    private function resolveAppliedCoupon(float $subtotal): ?Coupon
    {
        $couponId = session('checkout_coupon_id');
        if (!$couponId) {
            return null;
        }

        $coupon = Coupon::find($couponId);
        if (!$coupon || !$coupon->isUsable($subtotal)) {
            session()->forget('checkout_coupon_id');

            return null;
        }

        return $coupon;
    }
}
