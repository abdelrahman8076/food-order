@extends('layouts.admin')

@section('title', __('admin.settings.title'))

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.admin') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin.settings.breadcrumb') }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-sliders2-vertical text-accent-orange fs-4"></i>
            <h1 class="h3 m-0 fw-bold text-white">{{ __('admin.settings.heading') }}</h1>
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
                    <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">{{ __('admin.settings.store_profile') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.store_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="store_name" class="form-control" value="{{ old('store_name', $settings->store_name) }}" placeholder="{{ __('admin.settings.store_name_placeholder') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.whatsapp') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 text-success border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;"><i class="bi bi-whatsapp"></i></span>
                                <input type="text" name="store_phone" class="form-control" value="{{ old('store_phone', $settings->store_phone) }}" placeholder="+1234567890" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.hours') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;"><i class="bi bi-clock"></i></span>
                                <input type="text" name="store_hours" class="form-control" value="{{ old('store_hours', $settings->store_hours) }}" placeholder="{{ __('admin.settings.hours_placeholder') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.address') }}</label>
                            <textarea name="store_address" class="form-control" rows="3" placeholder="{{ __('admin.settings.address_placeholder') }}">{{ old('store_address', $settings->store_address) }}</textarea>
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
                        <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">{{ __('admin.settings.financials') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.tax_rate') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="tax_rate" class="form-control text-end font-monospace" value="{{ old('tax_rate', $settings->tax_rate) }}" step="0.01" min="0" max="1" placeholder="0.14" required>
                                    <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25" style="border-left: 1px solid var(--admin-border) !important;">
                                        {{ __('admin.settings.tax_equivalent', ['percent' => floatval($settings->tax_rate) * 100]) }}
                                    </span>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">{{ __('admin.settings.tax_hint') }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.default_delivery') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white bg-opacity-5 text-white-50 border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;">$</span>
                                    <input type="number" name="delivery_fee" class="form-control font-monospace" value="{{ old('delivery_fee', $settings->delivery_fee) }}" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">{{ __('admin.settings.default_delivery_hint') }}</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-white-50 small fw-semibold uppercase tracking-wider mb-2">{{ __('admin.settings.free_delivery_min') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white bg-opacity-5 text-white-50 border-secondary border-opacity-25" style="border-right: 1px solid var(--admin-border) !important;">$</span>
                                    <input type="number" name="free_delivery_min" class="form-control font-monospace" value="{{ old('free_delivery_min', $settings->free_delivery_min) }}" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div class="form-text text-muted fs-xs mt-1.5">{{ __('admin.settings.free_delivery_hint') }}</div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card border-0 bg-transparent">
                    <button type="submit" class="btn btn-admin-primary py-2.5 w-100 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                        <i class="bi bi-cloud-check-fill fs-5"></i> {{ __('admin.settings.save') }}
                    </button>
                </div>

            </div>
        </div>

    </div>
</form>
@endsection
