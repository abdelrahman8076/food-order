@extends('layouts.admin')

@section('title', __('admin.analytics.title'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="mb-0">{{ __('admin.analytics.title') }}</h1>
    <a href="{{ route('admin.analytics.export', request()->query()) }}" class="btn btn-admin-outline">
        <i class="bi bi-download me-1"></i> {{ __('admin.analytics.export') }}
    </a>
</div>

<div class="admin-card card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="row g-3 align-items-end">
            <div class="col-12">
                <label class="form-label">{{ __('admin.analytics.quick_range') }}</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['today' => __('admin.analytics.today'), '7d' => __('admin.analytics.last_7_days'), '30d' => __('admin.analytics.last_30_days'), 'month' => __('admin.analytics.this_month'), 'custom' => __('admin.analytics.custom')] as $key => $label)
                        <button type="submit" name="preset" value="{{ $key }}"
                                class="btn btn-sm {{ $preset === $key ? 'btn-admin-primary' : 'btn-admin-outline' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            @if($preset === 'custom')
                <div class="col-md-4">
                    <label class="form-label" for="from">{{ __('admin.analytics.from') }}</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ $fromInput }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="to">{{ __('admin.analytics.to') }}</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ $toInput }}" required>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="preset" value="custom">
                    <button type="submit" class="btn btn-admin-primary w-100">{{ __('common.apply') }}</button>
                </div>
            @endif
        </form>
        <p class="small text-muted mb-0 mt-3">
            {{ __('admin.analytics.period_note', ['from' => $from->format('M d, Y'), 'to' => $to->format('M d, Y')]) }}
        </p>
    </div>
</div>

@if($summary['orders'] === 0)
    <div class="admin-card card">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-0">{{ __('admin.analytics.empty') }}</p>
        </div>
    </div>
@else
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __('admin.analytics.revenue') }}</div>
                    <div class="fs-3 fw-bold" style="color: var(--admin-orange);">${{ number_format($summary['revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __('admin.analytics.orders') }}</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['orders']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __('admin.analytics.avg_order_value') }}</div>
                    <div class="fs-3 fw-bold text-white">${{ number_format($summary['avg_order_value'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __('admin.analytics.unique_customers') }}</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['customers']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">{{ __('admin.analytics.items_sold') }}</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['items_sold']) }}</div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.analytics._charts')

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="admin-card card h-100">
                <div class="card-header">{{ __('admin.analytics.top_customers') }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.analytics.col_customer') }}</th>
                                    <th>{{ __('admin.analytics.col_phone') }}</th>
                                    <th>{{ __('admin.analytics.col_orders') }}</th>
                                    <th class="text-end">{{ __('admin.analytics.col_spent') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer['name'] }}</td>
                                        <td>{{ $customer['phone'] }}</td>
                                        <td>{{ $customer['orders'] }}</td>
                                        <td class="text-end">${{ number_format($customer['spent'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">{{ __('admin.analytics.no_customer_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-card card h-100">
                <div class="card-header">{{ __('admin.analytics.coupon_performance') }}</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.analytics.col_code') }}</th>
                                    <th>{{ __('admin.analytics.col_uses') }}</th>
                                    <th class="text-end">{{ __('admin.analytics.col_discount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($couponStats as $coupon)
                                    <tr>
                                        <td>{{ $coupon['code'] }}</td>
                                        <td>{{ $coupon['uses'] }}</td>
                                        <td class="text-end">${{ number_format($coupon['total_discount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">{{ __('admin.analytics.no_coupons') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
