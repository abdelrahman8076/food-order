<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderByDesc('created_at')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', __('messages.coupon_created'));
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $this->validateCoupon($request, $coupon);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', __('messages.coupon_updated'));
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', __('messages.coupon_deleted'));
    }

    private function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => strtoupper(trim($request->input('code', '')))]);

        $valueRules = ['required', 'numeric', 'min:0.01'];
        if ($request->input('type') === 'percent') {
            $valueRules[] = 'max:100';
        }

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon?->id),
            ],
            'type' => 'required|in:percent,fixed',
            'value' => $valueRules,
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}
