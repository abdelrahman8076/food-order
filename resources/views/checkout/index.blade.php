@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
@php
    $savedForm = $checkoutForm ?? [];
    $formVal = fn (string $key, $default = '') => old($key, $savedForm[$key] ?? $default);
@endphp
<div class="container">
    <h1 class="page-heading mb-4">Checkout</h1>

    <div class="row g-4">
        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm" class="col-lg-7">
            @csrf
            <input type="hidden" name="order_type" value="delivery">
            <x-glass-card title="Your Details" class="mb-4">
                <div class="mb-3">
                    <label class="form-label text-white-50">Name *</label>
                    <input type="text" name="customer_name" class="form-control glass-input @error('customer_name') is-invalid @enderror"
                           value="{{ $formVal('customer_name', auth()->user()->name ?? '') }}" required>
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Phone *</label>
                    <input type="tel" name="customer_phone" class="form-control glass-input @error('customer_phone') is-invalid @enderror"
                           value="{{ $formVal('customer_phone', auth()->user()->phone ?? '') }}" required>
                    @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </x-glass-card>

            <x-glass-card title="Delivery Address">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-white-50">City *</label>
                        <select name="city_id" id="city_id" class="form-select glass-input @error('city_id') is-invalid @enderror" required>
                            <option value="">Select city</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ (string) $formVal('city_id') === (string) $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Area *</label>
                        <select name="area_id" id="area_id" class="form-select glass-input @error('area_id') is-invalid @enderror" required>
                            <option value="">Select area</option>
                        </select>
                        @error('area_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label text-white-50">Street *</label>
                        <input type="text" name="delivery_street" class="form-control glass-input @error('delivery_street') is-invalid @enderror"
                               value="{{ $formVal('delivery_street') }}" placeholder="e.g. 15 Abbas El Akkad St" required>
                        @error('delivery_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Building *</label>
                        <input type="text" name="delivery_building" class="form-control glass-input @error('delivery_building') is-invalid @enderror"
                               value="{{ $formVal('delivery_building') }}" placeholder="e.g. 3" required>
                        @error('delivery_building')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Floor</label>
                        <input type="text" name="delivery_floor" class="form-control glass-input @error('delivery_floor') is-invalid @enderror"
                               value="{{ $formVal('delivery_floor') }}" placeholder="Optional">
                        @error('delivery_floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Apartment</label>
                        <input type="text" name="delivery_apartment" class="form-control glass-input @error('delivery_apartment') is-invalid @enderror"
                               value="{{ $formVal('delivery_apartment') }}" placeholder="Optional">
                        @error('delivery_apartment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label text-white-50">Notes (optional)</label>
                    <textarea name="notes" class="form-control glass-input" rows="2" placeholder="Allergies, special requests...">{{ $formVal('notes') }}</textarea>
                </div>

                <p class="small text-white-50 mt-3 mb-0">
                    <i class="bi bi-cash me-1"></i> Pay with cash on delivery
                </p>
            </x-glass-card>
        </form>

        <div class="col-lg-5">
            <x-glass-card title="Order Summary">
                @foreach($items as $row)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between small">
                            <span class="text-white-50">{{ $row->name }} x {{ $row->quantity }}</span>
                            <span class="text-white">${{ number_format($row->total, 2) }}</span>
                        </div>
                        @if(!empty($row->notes))
                            <div class="small text-white-50 ps-2"><i class="bi bi-chat-left-text me-1"></i>{{ $row->notes }}</div>
                        @endif
                    </div>
                @endforeach
                <hr style="border-color: var(--glass-border);">

                @include('checkout._coupon')

                <div class="d-flex justify-content-between text-white-50"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                @if($discount > 0)
                    <div class="d-flex justify-content-between text-success">
                        <span>Discount ({{ $appliedCoupon->code }})</span>
                        <span>-${{ number_format($discount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between text-white-50"><span>Tax</span><span id="checkout-tax">${{ number_format($tax, 2) }}</span></div>
                <div class="d-flex justify-content-between text-white-50"><span>Delivery</span><span id="checkout-delivery">{{ $formVal('area_id') ? '$' . number_format($deliveryFee, 2) : '—' }}</span></div>
                <p class="small text-white-50 mb-0" id="checkout-delivery-note">
                    @if($formVal('area_id') && $deliveryFee > 0)
                        Delivery fee for orders under ${{ number_format($settings->free_delivery_min, 2) }}.
                    @elseif(!$formVal('area_id'))
                        Select your area to see the delivery fee.
                    @endif
                </p>
                <hr style="border-color: var(--glass-border);">
                <div class="d-flex justify-content-between fw-bold fs-5 text-white">
                    <span>Total</span><span id="checkout-total">${{ number_format($total, 2) }}</span>
                </div>
                <button type="submit" form="checkoutForm" class="btn btn-primary-orange w-100 mt-3">Place Order</button>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-glass w-100 mt-2">Back to Cart</a>
            </x-glass-card>
        </div>
    </div>
</div>

<div class="sticky-checkout-bar">
    <div class="container">
        @if($appliedCoupon)
            @include('checkout._coupon', ['compact' => true])
        @endif
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="small text-white-50">Total</div>
                <div class="fw-bold fs-5 text-white" id="checkout-sticky-total">${{ number_format($total, 2) }}</div>
            </div>
            <button type="submit" form="checkoutForm" class="btn btn-primary-orange px-4">Place Order</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const areasByCity = @json($areasByCity);
    const subtotal = @json($subtotal);
    const discount = @json($discount);
    const tax = @json($tax);
    const freeDeliveryMin = @json((float) $settings->free_delivery_min);
    const freeDeliveryNote = @json($freeDeliveryNote);

    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');
    const deliveryEl = document.getElementById('checkout-delivery');
    const deliveryNoteEl = document.getElementById('checkout-delivery-note');
    const totalEl = document.getElementById('checkout-total');
    const stickyTotalEl = document.getElementById('checkout-sticky-total');
    const oldAreaId = @json($formVal('area_id') ?: null);

    function syncCheckoutFieldsToCouponForm(couponForm) {
        const checkoutForm = document.getElementById('checkoutForm');
        if (!checkoutForm || !couponForm) return;

        couponForm.querySelectorAll('input[data-checkout-sync]').forEach(function(el) {
            el.remove();
        });

        checkoutForm.querySelectorAll('input, select, textarea').forEach(function(field) {
            if (!field.name || field.name === '_token' || field.name === 'order_type') return;

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = field.name;
            hidden.value = field.value;
            hidden.setAttribute('data-checkout-sync', '1');
            couponForm.appendChild(hidden);
        });
    }

    document.querySelectorAll('form[action*="checkout/coupon"]').forEach(function(couponForm) {
        couponForm.addEventListener('submit', function() {
            syncCheckoutFieldsToCouponForm(couponForm);
        });
    });

    function formatMoney(amount) {
        return '$' + amount.toFixed(2);
    }

    function findArea(cityId, areaId) {
        if (!cityId || !areaId || !areasByCity[cityId]) return null;
        return areasByCity[cityId].find(function(area) {
            return String(area.id) === String(areaId);
        }) || null;
    }

    function calculateDeliveryFee(area) {
        if (!area) return null;
        if (subtotal >= freeDeliveryMin) return 0;
        return area.delivery_fee;
    }

    function updateTotals(area) {
        const deliveryFee = calculateDeliveryFee(area);
        const discountedSubtotal = Math.max(0, subtotal - discount);
        const total = discountedSubtotal + tax + (deliveryFee ?? 0);

        if (deliveryFee === null) {
            deliveryEl.textContent = '—';
            deliveryNoteEl.textContent = 'Select your area to see the delivery fee.';
        } else {
            deliveryEl.textContent = formatMoney(deliveryFee);
            if (deliveryFee > 0) {
                deliveryNoteEl.textContent = freeDeliveryNote;
            } else if (subtotal >= freeDeliveryMin) {
                deliveryNoteEl.textContent = 'Free delivery — your order qualifies.';
            } else {
                deliveryNoteEl.textContent = 'Free delivery for this area.';
            }
        }

        totalEl.textContent = formatMoney(total);
        if (stickyTotalEl) {
            stickyTotalEl.textContent = formatMoney(total);
        }
    }

    function populateAreas(cityId, selectedAreaId) {
        areaSelect.innerHTML = '<option value="">Select area</option>';
        if (!cityId || !areasByCity[cityId]) {
            updateTotals(null);
            return;
        }

        areasByCity[cityId].forEach(function(area) {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.name;
            if (selectedAreaId && String(selectedAreaId) === String(area.id)) {
                option.selected = true;
            }
            areaSelect.appendChild(option);
        });

        updateTotals(findArea(cityId, areaSelect.value));
    }

    citySelect.addEventListener('change', function() {
        populateAreas(this.value, null);
    });

    areaSelect.addEventListener('change', function() {
        updateTotals(findArea(citySelect.value, this.value));
    });

    if (citySelect.value) {
        populateAreas(citySelect.value, oldAreaId);
    } else {
        updateTotals(null);
    }
})();
</script>
@endpush
@endsection
