@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<!-- Page Header and Live Indicator -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Orders Platform</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h1 class="h3 m-0 fw-bold text-white">Live Kitchen Board</h1>
            <x-admin.status-pill variant="success" class="gap-1">
                <span class="live-dot-pulse"></span> Streaming Live
            </x-admin.status-pill>
        </div>
    </div>
</div>

<!-- Section A: Live Real-Time Production Columns -->
<div class="row g-3 mb-5" id="kitchenBoardColumns">
    @include('admin.orders._kitchen-board-columns')
</div>

@include('admin.orders._kitchen-board-poll')

<hr class="border-secondary border-opacity-10 my-5">

<!-- Section B: Historical Log / Advanced Audit Ledger -->
<div class="d-flex align-items-center gap-2 mb-3">
    <i class="bi bi-clock-history text-muted fs-5"></i>
    <h2 class="h5 m-0 fw-bold text-white">All Master Orders</h2>
</div>

<!-- Integrated Control Bar Block Component -->
<div class="card mb-4 border-0">
    <div class="card-body p-3 style-filter-bg">
        <form action="{{ url()->current() }}" method="GET" class="m-0">
            <div class="row g-2 align-items-center">
                
                <!-- Order Number Input Tag Search -->
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25"><i class="bi bi-search"></i></span>
                        <input type="text" name="order_number" class="form-control form-control-sm" placeholder="Search order number..." value="{{ request('order_number') }}">
                    </div>
                </div>

                <!-- Status Context Selector -->
                <div class="col-12 col-sm-4 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25"><i class="bi bi-funnel"></i></span>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Fulfillment Statuses</option>
                            @foreach(array_merge(\App\Services\OrderService::STATUSES, ['cancelled' => 'Cancelled']) as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Control Submit Modifiers Trigger Actions -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-admin-primary px-3">Apply Filter</button>
                    @if(request()->has('order_number') || request()->has('status'))
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-admin-outline px-2 ms-1" title="Clear configuration filters">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Master Order Log Grid Card Layout Architecture -->
<div class="admin-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 admin-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Order ID</th>
                    <th style="width: 25%;">Customer Details</th>
                    <th style="width: 13%;">Distribution Type</th>
                    <th style="width: 15%;">Fulfillment Status</th>
                    <th style="width: 12%;">Financial Total</th>
                    <th style="width: 15%;">Submission Date</th>
                    <th style="width: 8%;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <!-- Order Identification Token -->
                        <td>
                            <span class="text-accent-yellow font-monospace fw-bold">
                                #{{ $order->order_number }}
                            </span>
                        </td>

                        <!-- Detailed Stacked Customer Metadata Field -->
                        <td>
                            <div class="truncate-container">
                                <h6 class="text-white fw-semibold mb-0 text-truncate">{{ $order->customer_name }}</h6>
                                <a href="tel:{{ $order->customer_phone }}" class="text-muted small text-decoration-none hover-orange d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-telephone text-opacity-50 fs-xs"></i>{{ $order->customer_phone }}
                                </a>
                            </div>
                        </td>

                        <!-- Distribution Channel Indicators -->
                        <td>
                            @if(strtolower($order->order_type) === 'delivery')
                                <span class="text-info small fw-medium d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-truck"></i> Delivery
                                </span>
                            @else
                                <span class="text-accent-orange small fw-medium d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-bag-heart"></i> Takeout
                                </span>
                            @endif
                        </td>

                        <!-- Configured Semantic Status Badge Mapping Switch Context -->
                        <td>
                            @php
                                $statusRaw = strtolower($order->status);
                                $statusLabel = \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status);
                                $statusVariant = match($statusRaw) {
                                    'pending' => 'warning',
                                    'completed', 'delivered' => 'success',
                                    'cancelled', 'failed' => 'danger',
                                    default => 'info',
                                };
                            @endphp
                            <x-admin.status-pill :variant="$statusVariant">{{ $statusLabel }}</x-admin.status-pill>
                        </td>

                        <!-- Financial Line Cost Fields -->
                        <td>
                            <span class="text-white fw-bold">${{ number_format($order->total, 2) }}</span>
                        </td>

                        <!-- System Timestamps Formatted Strings -->
                        <td>
                            <div class="small text-white-50">{{ $order->created_at->format('M d, Y') }}</div>
                            <div class="text-muted fs-xs" style="margin-top: -2px;">{{ $order->created_at->format('H:i') }} hrs</div>
                        </td>

                        <!-- Action Controls Row Grid Block Linking -->
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-icon-action btn-view-tint" title="Inspect complete order logs">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted display-5 mb-2"><i class="bi bi-clipboard-x opacity-25"></i></div>
                            <h6 class="text-white fw-semibold">No Orders Registered</h6>
                            <p class="text-muted small max-w-xs mx-auto mb-0">No entries currently match that specific tracking signature parameters layout.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- System Navigation Row Pagination Selector wrapper block -->
<div class="mt-4 custom-pagination-wrapper">
    {{ $orders->withQueryString()->links() }}
</div>
@endsection