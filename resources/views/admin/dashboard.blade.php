@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h1 class="mb-0">Dashboard</h1>
    <a href="{{ route('admin.analytics.index') }}" class="btn btn-admin-outline">View full analytics <i class="bi bi-arrow-right ms-1"></i></a>
</div>

<h5 class="mb-3">This Month at a Glance <span class="text-muted small">(delivered orders)</span></h5>
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">Revenue</div>
                <div class="fs-4 fw-bold" style="color: var(--admin-orange);">${{ number_format($monthSummary['revenue'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">Orders</div>
                <div class="fs-4 fw-bold text-white">{{ number_format($monthSummary['orders']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">Unique Customers</div>
                <div class="fs-4 fw-bold text-white">{{ number_format($monthSummary['customers']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card card h-100">
            <div class="card-body">
                <div class="small text-muted">Top Item</div>
                @if($monthSummary['top_item_name'])
                    <div class="fw-bold text-white">{{ $monthSummary['top_item_name'] }}</div>
                    <div class="small text-muted">{{ number_format($monthSummary['top_item_quantity']) }} sold</div>
                @else
                    <div class="text-muted">—</div>
                @endif
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3">Operations</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="admin-card card h-100 border-start border-4 border-primary">
            <div class="card-body">
                <h5 class="card-title text-muted">Orders Today</h5>
                <p class="display-6 mb-0 text-white">{{ $todayOrders }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card card h-100 border-start border-4 border-warning">
            <div class="card-body">
                <h5 class="card-title text-muted">Pending Orders</h5>
                <p class="display-6 mb-0 text-white">{{ $pendingOrders }}</p>
            </div>
        </div>
    </div>
</div>

<h5 class="mb-3">Recent Orders</h5>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
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
                                    $statusLabel = \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status);
                                @endphp
                                <x-admin.status-pill :variant="$statusVariant">{{ $statusLabel }}</x-admin.status-pill>
                            </td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>{{ $order->created_at->format('M d, H:i') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-admin-outline">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
