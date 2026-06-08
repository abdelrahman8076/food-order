@extends('layouts.admin')

@section('title', 'Analytics')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="mb-0">Analytics</h1>
    <a href="{{ route('admin.analytics.export', request()->query()) }}" class="btn btn-admin-outline">
        <i class="bi bi-download me-1"></i> Export CSV
    </a>
</div>

<div class="admin-card card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="row g-3 align-items-end">
            <div class="col-12">
                <label class="form-label">Quick range</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(['today' => 'Today', '7d' => 'Last 7 days', '30d' => 'Last 30 days', 'month' => 'This month', 'custom' => 'Custom'] as $key => $label)
                        <button type="submit" name="preset" value="{{ $key }}"
                                class="btn btn-sm {{ $preset === $key ? 'btn-admin-primary' : 'btn-admin-outline' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
            @if($preset === 'custom')
                <div class="col-md-4">
                    <label class="form-label" for="from">From</label>
                    <input type="date" name="from" id="from" class="form-control" value="{{ $fromInput }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="to">To</label>
                    <input type="date" name="to" id="to" class="form-control" value="{{ $toInput }}" required>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="preset" value="custom">
                    <button type="submit" class="btn btn-admin-primary w-100">Apply</button>
                </div>
            @endif
        </form>
        <p class="small text-muted mb-0 mt-3">
            Showing delivered orders from {{ $from->format('M d, Y') }} to {{ $to->format('M d, Y') }}.
        </p>
    </div>
</div>

@if($summary['orders'] === 0)
    <div class="admin-card card">
        <div class="card-body text-center py-5">
            <p class="text-muted mb-0">No delivered orders in this period.</p>
        </div>
    </div>
@else
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">Revenue</div>
                    <div class="fs-3 fw-bold" style="color: var(--admin-orange);">${{ number_format($summary['revenue'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">Orders</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['orders']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">Avg Order Value</div>
                    <div class="fs-3 fw-bold text-white">${{ number_format($summary['avg_order_value'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">Unique Customers</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['customers']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="admin-card card h-100">
                <div class="card-body">
                    <div class="small text-muted">Items Sold</div>
                    <div class="fs-3 fw-bold text-white">{{ number_format($summary['items_sold']) }}</div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.analytics._charts')

    <div class="row g-4 mt-1">
        <div class="col-lg-6">
            <div class="admin-card card h-100">
                <div class="card-header">Top Customers</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Orders</th>
                                    <th class="text-end">Spent</th>
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
                                    <tr><td colspan="4" class="text-muted">No customer data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-card card h-100">
                <div class="card-header">Coupon Performance</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover admin-table mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Uses</th>
                                    <th class="text-end">Discount Given</th>
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
                                    <tr><td colspan="3" class="text-muted">No coupons used in this period.</td></tr>
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
