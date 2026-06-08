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
    private const CHECKOUT_FORM_KEYS = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'city_id',
        'area_id',
        'delivery_street',
        'delivery_building',
        'delivery_floor',
        'delivery_apartment',
        'notes',
    ];

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

        $areaDeliveryFee = null;
        $hasSelectedArea = false;
        if ($oldAreaId = old('area_id', session('checkout_form.area_id'))) {
            $area = DeliveryArea::active()->find($oldAreaId);
            if ($area) {
                $areaDeliveryFee = (float) $area->delivery_fee;
                $hasSelectedArea = true;
            }
        }

        $pricing = $this->orderService->calculatePricing($built['subtotal'], 'delivery', $discount, $areaDeliveryFee);
        $deliveryFee = $hasSelectedArea ? $pricing['deliveryFee'] : 0;
        $total = $pricing['discountedSubtotal'] + $pricing['tax'] + $deliveryFee;
        $cities = City::active()->with('activeAreas')->orderBy('name')->get();
        $areasByCity = $cities->mapWithKeys(fn ($city) => [
            $city->id => $city->activeAreas->map(fn ($area) => [
                'id' => $area->id,
                'name' => $area->name,
                'delivery_fee' => (float) $area->delivery_fee,
            ])->values(),
        ]);

        return view('checkout.index', [
            'items' => $built['items'],
            'subtotal' => $built['subtotal'],
            'tax' => $pricing['tax'],
            'deliveryFee' => $deliveryFee,
            'discount' => $pricing['discount'],
            'total' => $total,
            'settings' => $settings,
            'cities' => $cities,
            'areasByCity' => $areasByCity,
            'freeDeliveryNote' => 'Delivery fee for orders under $' . number_format($settings->free_delivery_min, 2) . '.',
            'appliedCoupon' => $appliedCoupon,
            'checkoutForm' => session('checkout_form', []),
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
            return $this->flashCheckoutForm($request)->with('error', 'Invalid coupon code.');
        }

        try {
            $this->couponService->apply($coupon, $built['subtotal']);
        } catch (InvalidArgumentException $e) {
            return $this->flashCheckoutForm($request)->with('error', $e->getMessage());
        }

        session()->put('checkout_coupon_id', $coupon->id);

        return $this->flashCheckoutForm($request)->with('success', 'Coupon ' . $coupon->code . ' applied.');
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('checkout_coupon_id');

        return $this->flashCheckoutForm($request)->with('success', 'Coupon removed.');
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

    private function flashCheckoutForm(Request $request)
    {
        $input = $request->only(self::CHECKOUT_FORM_KEYS);
        session()->put('checkout_form', $input);

        return back()->withInput($input);
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
