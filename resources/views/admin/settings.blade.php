@extends('layouts.admin')

@section('title', 'Store Settings')

@section('content')
<h1 class="mb-4">Store Settings</h1>

<div class="admin-card card">
    <div class="card-body">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Store Name</label>
                    <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings->store_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone (WhatsApp)</label>
                    <input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings->store_phone) }}" placeholder="+1234567890">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="store_address" class="form-control" rows="2">{{ old('store_address', $settings->store_address) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hours</label>
                    <input type="text" name="store_hours" class="form-control" value="{{ old('store_hours', $settings->store_hours) }}" placeholder="Mon-Sat 11am-8pm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tax Rate (e.g. 0.10 = 10%)</label>
                    <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', $settings->tax_rate) }}" step="0.01" min="0" max="1" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Delivery Fee ($)</label>
                    <input type="number" name="delivery_fee" class="form-control" value="{{ old('delivery_fee', $settings->delivery_fee) }}" step="0.01" min="0" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Free Delivery Minimum ($)</label>
                    <input type="number" name="free_delivery_min" class="form-control" value="{{ old('free_delivery_min', $settings->free_delivery_min) }}" step="0.01" min="0" required>
                </div>
            </div>
            <button type="submit" class="btn btn-admin-primary mt-4">Save Settings</button>
        </form>
    </div>
</div>
@endsection
