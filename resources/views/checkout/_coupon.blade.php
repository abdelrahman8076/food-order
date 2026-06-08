@php
    $compact = $compact ?? false;
@endphp

@if($appliedCoupon)
    <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
        <span class="small text-white-50">
            @if($compact)
                <strong class="text-success">{{ $appliedCoupon->code }}</strong>
                <span class="text-success">−${{ number_format($discount, 2) }}</span>
            @else
                {{ __('checkout.coupon_applied', ['code' => $appliedCoupon->code]) }}
            @endif
        </span>
        <form action="{{ route('checkout.coupon.remove') }}" method="POST" class="d-inline flex-shrink-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-glass">{{ __('checkout.coupon_remove') }}</button>
        </form>
    </div>
@elseif(!$compact)
    <form action="{{ route('checkout.coupon.apply') }}" method="POST" class="mb-3">
        @csrf
        <label class="form-label text-white-50 small">{{ __('checkout.coupon_code') }}</label>
        <div class="input-group">
            <input type="text" name="code" class="form-control glass-input" placeholder="{{ __('checkout.coupon_placeholder') }}" value="{{ old('code') }}">
            <button type="submit" class="btn btn-outline-glass">{{ __('checkout.coupon_apply') }}</button>
        </div>
    </form>
@endif
