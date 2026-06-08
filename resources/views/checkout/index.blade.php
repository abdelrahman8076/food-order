@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
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
                           value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label text-white-50">Phone *</label>
                    <input type="tel" name="customer_phone" class="form-control glass-input @error('customer_phone') is-invalid @enderror"
                           value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
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
                                <option value="{{ $city->id }}" {{ (string) old('city_id') === (string) $city->id ? 'selected' : '' }}>
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
                               value="{{ old('delivery_street') }}" placeholder="e.g. 15 Abbas El Akkad St" required>
                        @error('delivery_street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white-50">Building *</label>
                        <input type="text" name="delivery_building" class="form-control glass-input @error('delivery_building') is-invalid @enderror"
                               value="{{ old('delivery_building') }}" placeholder="e.g. 3" required>
                        @error('delivery_building')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Floor</label>
                        <input type="text" name="delivery_floor" class="form-control glass-input @error('delivery_floor') is-invalid @enderror"
                               value="{{ old('delivery_floor') }}" placeholder="Optional">
                        @error('delivery_floor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-white-50">Apartment</label>
                        <input type="text" name="delivery_apartment" class="form-control glass-input @error('delivery_apartment') is-invalid @enderror"
                               value="{{ old('delivery_apartment') }}" placeholder="Optional">
                        @error('delivery_apartment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label text-white-50">Notes (optional)</label>
                    <textarea name="notes" class="form-control glass-input" rows="2" placeholder="Allergies, special requests...">{{ old('notes') }}</textarea>
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
                <div class="d-flex justify-content-between text-white-50"><span>Tax</span><span>${{ number_format($tax, 2) }}</span></div>
                <div class="d-flex justify-content-between text-white-50"><span>Delivery</span><span>${{ number_format($deliveryFee, 2) }}</span></div>
                @if($deliveryFee > 0)
                    <p class="small text-white-50 mb-0">Delivery fee for orders under ${{ number_format($settings->free_delivery_min, 2) }}.</p>
                @endif
                <hr style="border-color: var(--glass-border);">
                <div class="d-flex justify-content-between fw-bold fs-5 text-white">
                    <span>Total</span><span>${{ number_format($total, 2) }}</span>
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
                <div class="fw-bold fs-5 text-white">${{ number_format($total, 2) }}</div>
            </div>
            <button type="submit" form="checkoutForm" class="btn btn-primary-orange px-4">Place Order</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const areasByCity = @json($cities->mapWithKeys(fn ($city) => [
        $city->id => $city->activeAreas->map(fn ($area) => ['id' => $area->id, 'name' => $area->name])->values(),
    ]));
    const citySelect = document.getElementById('city_id');
    const areaSelect = document.getElementById('area_id');
    const oldAreaId = @json(old('area_id'));

    function populateAreas(cityId, selectedAreaId) {
        areaSelect.innerHTML = '<option value="">Select area</option>';
        if (!cityId || !areasByCity[cityId]) return;

        areasByCity[cityId].forEach(function(area) {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.name;
            if (selectedAreaId && String(selectedAreaId) === String(area.id)) {
                option.selected = true;
            }
            areaSelect.appendChild(option);
        });
    }

    citySelect.addEventListener('change', function() {
        populateAreas(this.value, null);
    });

    if (citySelect.value) {
        populateAreas(citySelect.value, oldAreaId);
    }
})();
</script>
@endpush
@endsection
