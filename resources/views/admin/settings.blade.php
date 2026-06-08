@extends('layouts.admin')

@section('title', 'Store Settings')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Global Configurations</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-sliders2-vertical text-accent-orange fs-4"></i>
            <h1 class="h3 m-0 fw-bold text-white">Store Configurations</h1>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" class="m-0">
    @csrf
    
    <div class="row g-4">
        
        <div class="col-xl-7 col-lg-6">
            <div class="admin-card card border-0 h-100">
                <div class="card-header py-3 d-flex align-items-center gap-2 border-bottom style-header-border bg-white bg-opacity-1">
                    <i class="bi bi-shop text-accent-orange"></i>
                    <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">Store Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Store Frontend Name <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings->store_name) }}" placeholder="e.g. Gourmet Sushi Bar" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">WhatsApp Line Access <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 text-success border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings->store_phone) }}" placeholder="+1234567890" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Operational Schedule Timelines</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;"><i class="bi bi-clock"></i></span>
                                <input type="text" name="store_hours" class="form-control" value="{{ old('store_hours', $settings->store_hours) }}" placeholder="e.g. Mon-Sat 11am-8pm, Sun Closed">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Physical Headquarters Address</label>
                            <textarea name="store_address" class="form-control" rows="3" placeholder="Provide complete storefront checkout addresses details...">{{ old('store_address', $settings->store_address) }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6">
            <div class="d-flex flex-column gap-4 h-100">
                
                <div class="admin-card card border-0 flex-grow-1">
                    <div class="card-header py-3 d-flex align-items-center gap-2 border-bottom style-header-border bg-white bg-opacity-1">
                        <i class="bi bi-cash-coin text-accent-yellow"></i>
                        <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">Financials & Logistics</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Taxation Rate Matrix <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="tax_rate" class="form-control text-end font-monospace" value="{{ old('tax_rate', $settings->tax_rate) }}" step="0.01" min="0" max="1" placeholder="0.14" required>
                                    <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25" style="border-left: 1px solid var(--admin-border) !important;">
                                        = {{ floatval($settings->tax_rate) * 100 }}% VAT Equivalent
                                    </span>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">Represent ratio parameters using decimals ($0.14 = 14\%$).</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Default Base Delivery Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white bg-opacity-5 text-white-50 border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;">$</span>
                                    <input type="number" name="delivery_fee" class="form-control font-monospace" value="{{ old('delivery_fee', $settings->delivery_fee) }}" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">Failsafe fallback shipping fee applied automatically when initializing new region sectors.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">Free Carriage Threshold Level <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white bg-opacity-5 text-white-50 border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;">$</span>
                                    <input type="number" name="free_delivery_min" class="form-control font-monospace" value="{{ old('free_delivery_min', $settings->free_delivery_min) }}" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">Minimum cart subtotal required to override delivery fees and apply free shipping.</div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-transparent">
                    <button type="submit" class="btn btn-admin-primary py-2.5 w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <i class="bi bi-cloud-check-fill fs-5"></i> Commit Platform Settings Updates
                    </button>
                </div>

            </div>
        </div>

    </div>
</form>

@push('styles')
<style>
    .style-header-border { border-color: rgba(255, 255, 255, 0.06) !important; }
    .mt-1\.5 { margin-top: 0.4rem !important; }
</style>
@endpush
@endsection