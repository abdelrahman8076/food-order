@php
    $isEdit = $coupon !== null;
@endphp

<form action="{{ $isEdit ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.code') }}</label>
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code', $coupon->code ?? '') }}" placeholder="{{ __('admin.coupons.code_placeholder') }}" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.type') }}</label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="percent" {{ old('type', $coupon->type ?? '') === 'percent' ? 'selected' : '' }}>{{ __('admin.coupons.type_percent') }}</option>
                <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>{{ __('admin.coupons.type_fixed') }}</option>
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.value') }}</label>
            <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value', $coupon->value ?? '') }}" step="0.01" min="0.01" required>
            <small class="text-muted">{{ __('admin.coupons.value_hint') }}</small>
            @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.min_order') }}</label>
            <input type="number" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror"
                   value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" step="0.01" min="0">
            @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.max_uses') }}</label>
            <input type="number" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror"
                   value="{{ old('max_uses', $coupon->max_uses ?? '') }}" min="1">
            @error('max_uses')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin.coupons.expires_at') }}</label>
            <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
                   value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
            @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                       {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('common.active') }}</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-admin-primary">{{ $isEdit ? __('admin.coupons.update') : __('admin.coupons.create') }}</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-admin-outline">{{ __('common.cancel') }}</a>
    </div>
</form>
