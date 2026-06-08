@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-4 py-md-5">
    
    <!-- Page Header Context -->
    <div class="mb-4">
        <span class="text-uppercase tracking-widest small text-warning fw-bold mb-1 d-block">Purchase History</span>
        <h1 class="display-5 fw-black text-white m-0">My <span class="italic-serif text-accent-yellow">Orders</span></h1>
        <p class="text-white-50 small mt-1 mb-0">Review past delicious journeys or track your fresh requests arriving live.</p>
    </div>

    @forelse($orders as $order)
        @php
            $statusVariant = match($order->status) {
                'pending' => 'warning',
                'delivered' => 'success',
                'cancelled' => 'danger',
                'processing', 'preparing', 'confirmed', 'ready', 'out_for_delivery' => 'info',
                default => 'primary',
            };
            $statusLabel = \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status);
        @endphp

        <x-glass-card class="mb-3 order-history-card position-relative overflow-hidden">
            <div class="row align-items-center g-3">
                
                <!-- Main Metadata Block -->
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="history-icon-wrapper d-none d-sm-flex">
                            <i class="bi bi-receipt text-white-50"></i>
                        </div>
                        <div>
                            <h6 class="text-white fw-bold tracking-tight mb-1 fs-5 order-number-txt">{{ $order->order_number }}</h6>
                            <span class="text-white-50 small d-block"><i class="bi bi-clock me-1 small"></i>{{ $order->created_at->format('M d, Y · g:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Item Elements Tag Cloud Block -->
                <div class="col-md-5">
                    <div class="d-flex flex-wrap gap-1.5 align-items-center item-pill-container">
                        @foreach($order->orderItems as $oi)
                            <span class="item-tag-pill bg-white bg-opacity-5 text-white-70 border border-white border-opacity-10 rounded-pill px-2.5 py-1 small">
                                <span class="fw-bold text-accent-yellow me-1">{{ $oi->quantity }}x</span>{{ $oi->item_name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Financial Data Actions Stack -->
                <div class="col-md-3 text-md-end d-flex flex-row flex-md-column justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-3 flex-row-reverse flex-md-row">
                        <x-public.status-pill :variant="$statusVariant">{{ $statusLabel }}</x-public.status-pill>
                        <div class="text-white font-serif fw-bold fs-5">${{ number_format($order->total, 2) }}</div>
                    </div>
                    
                    <a href="{{ route('order.track') }}?order_number={{ $order->order_number }}" 
                       class="btn btn-sm btn-action-track px-3 py-1.5 rounded-pill w-100-mobile transition-all">
                        <i class="bi bi-geo-alt me-1"></i>Track Order
                    </a>
                </div>

            </div>
        </x-glass-card>
    @empty
        <!-- Styled Empty State Block -->
        <x-glass-card class="text-center py-5 border border-dashed border-secondary border-opacity-25 rounded-4">
            <div class="mb-3 text-muted display-5"><i class="bi bi-bag-x"></i></div>
            <h5 class="text-white fw-bold mb-2">No Orders Registered</h5>
            <p class="text-white-50 m-0 max-w-sm mx-auto fs-6 mb-4">
                Looks like you haven't placed any delicious requests within your history yet. Let's fix that!
            </p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary-orange px-4 py-2 fw-bold text-uppercase tracking-wider rounded-pill shadow-sm">
                <i class="bi bi-search me-2 small"></i>Browse Our Kitchen Menu
            </a>
        </x-glass-card>
    @endforelse

    <!-- Premium Clean Layout Pagination Links Wrapper -->
    <div class="mt-4 custom-pagination-wrapper">{{ $orders->links() }}</div>
</div>
@endsection
