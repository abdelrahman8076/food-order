@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container">
    <h1 class="mb-4">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header">Your details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                            @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                            @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email (optional)</label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email', auth()->user()->email ?? '') }}">
                            @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">Order type</div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="order_type" id="type_delivery" value="delivery" {{ old('order_type', 'delivery') === 'delivery' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_delivery">Delivery</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="order_type" id="type_pickup" value="pickup" {{ old('order_type') === 'pickup' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_pickup">Pickup</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="order_type" id="type_dine_in" value="dine_in" {{ old('order_type') === 'dine_in' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_dine_in">Dine in</label>
                        </div>

                        <div id="delivery_address_block" class="mt-2">
                            <label class="form-label">Delivery address *</label>
                            <textarea name="delivery_address" class="form-control @error('delivery_address') is-invalid @enderror" rows="2">{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div id="table_number_block" class="mt-2 d-none">
                            <label class="form-label">Table number *</label>
                            <input type="text" name="table_number" class="form-control @error('table_number') is-invalid @enderror" value="{{ old('table_number') }}">
                            @error('table_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mt-2">
                            <label class="form-label">Special instructions (optional)</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card sticky-top">
                    <div class="card-header">Order summary</div>
                    <div class="card-body">
                        @foreach($items as $row)
                            <div class="d-flex justify-content-between small">
                                <span>{{ $row->name }} x {{ $row->quantity }}</span>
                                <span>${{ number_format($row->total, 2) }}</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-between"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                        <div class="d-flex justify-content-between"><span>Tax (10%)</span><span>${{ number_format($tax, 2) }}</span></div>
                        <div class="d-flex justify-content-between"><span>Delivery fee</span><span>${{ number_format($deliveryFee, 2) }}</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span>${{ number_format($total, 2) }}</span></div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Place Order</button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">Back to Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="order_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.getElementById('delivery_address_block').classList.toggle('d-none', this.value !== 'delivery');
        document.getElementById('table_number_block').classList.toggle('d-none', this.value !== 'dine_in');
    });
});
var checked = document.querySelector('input[name="order_type"]:checked');
if (checked) checked.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
