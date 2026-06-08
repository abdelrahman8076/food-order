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
            <label class="form-label">Code *</label>
            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code', $coupon->code ?? '') }}" placeholder="e.g. WELCOME10" required>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Type *</label>
            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="percent" {{ old('type', $coupon->type ?? '') === 'percent' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
            </select>
            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Value *</label>
            <input type="number" name="value" class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value', $coupon->value ?? '') }}" step="0.01" min="0.01" required>
            <small class="text-muted">Percent: 1–100. Fixed: dollar amount.</small>
            @error('value')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Minimum order amount</label>
            <input type="number" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror"
                   value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}" step="0.01" min="0">
            @error('min_order_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Max uses</label>
            <input type="number" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror"
                   value="{{ old('max_uses', $coupon->max_uses ?? '') }}" min="1">
            @error('max_uses')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Expires at</label>
            <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
                   value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
            @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1"
                       {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-admin-primary">{{ $isEdit ? 'Update Coupon' : 'Create Coupon' }}</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-admin-outline">Cancel</a>
    </div>
</form>
