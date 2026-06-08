@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="mb-0">{{ __('admin.dashboard.title') }}</h1>
    <a href="{{ route('admin.analytics.index') }}" class="btn btn-admin-outline">{{ __('admin.dashboard.view_analytics') }} <i class="bi bi-arrow-right ms-1"></i></a>
</div>

<h5 class="mb-3">{{ __('admin.dashboard.month_glance') }} <span class="text-muted small">{{ __('admin.dashboard.delivered_orders_note') }}</span></h5>
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">{{ __('admin.dashboard.revenue') }}</div>
                <div class="fs-4 fw-bold" style="color: var(--admin-orange);">${{ number_format($monthSummary['revenue'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">{{ __('admin.dashboard.orders') }}</div>
                <div class="fs-4 fw-bold text-white">{{ number_format($monthSummary['orders']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">{{ __('admin.dashboard.unique_customers') }}</div>
                <div class="fs-4 fw-bold text-white">{{ number_format($monthSummary['customers']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">{{ __('admin.dashboard.top_item') }}</div>
                @if($monthSummary['top_item_name'])
                    <div class="fw-bold text-white">{{ $monthSummary['top_item_name'] }}</div>
                    <div class="small text-muted">{{ number_format($monthSummary['top_item_quantity']) }} {{ __('admin.dashboard.sold') }}</div>
                @else
                    <div class="text-muted">—</div>
                @endif
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3">{{ __('admin.dashboard.operations') }}</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-card card h-100 border-start border-4 border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">{{ __('admin.dashboard.orders_today') }}</h5>
                <p class="display-6 mb-0 text-white">{{ $todayOrders }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card card h-100 border-start border-4 border-warning">
            <div class="card-body">
                <h5 class="card-title text-muted">{{ __('admin.dashboard.pending_orders') }}</h5>
                <p class="display-6 mb-0 text-white">{{ $pendingOrders }}</p>
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3">{{ __('admin.dashboard.recent_orders') }}</h5>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('admin.dashboard.col_order') }}</th>
                        <th>{{ __('admin.dashboard.col_customer') }}</th>
                        <th>{{ __('admin.dashboard.col_type') }}</th>
                        <th>{{ __('admin.dashboard.col_status') }}</th>
                        <th>{{ __('admin.dashboard.col_total') }}</th>
                        <th>{{ __('admin.dashboard.col_date') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ ucfirst($order->order_type) }}</td>
                            <td>
                                @php
                                    $statusVariant = match($order->status) {
                                        'pending' => 'warning',
                                        'delivered', 'completed' => 'success',
                                        'cancelled', 'failed' => 'danger',
                                        default => 'info',
                                    };
                                    $statusLabel = \App\Services\OrderService::statusLabel($order->status);
                                @endphp
                                <x-admin.status-pill :variant="$statusVariant">{{ $statusLabel }}</x-admin.status-pill>
                            </td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>{{ $order->created_at->format('M d, H:i') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-admin-outline">{{ __('common.view') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">{{ __('admin.dashboard.no_orders') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
