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

@push('styles')
<style>
    /* Global Helpers Alignment Variables fallback definitions */
    :root {
        --accent-yellow: #ffc107;
        --primary-orange: #ff7f50;
    }
    .fw-black { font-weight: 900; }
    .tracking-widest { letter-spacing: 2.5px; }
    .tracking-wider { letter-spacing: 1.25px; }
    .tracking-tight { letter-spacing: -0.5px; }
    .italic-serif, .font-serif { font-family: 'Playfair Display', Georgia, serif; font-style: italic; }
    .gap-1\.5 { gap: 0.35rem !important; }
    .px-2\.5 { padding-left: 0.65rem !important; padding-right: 0.65rem !important; }
    .py-1\.5 { padding-top: 0.35rem !important; padding-bottom: 0.35rem !important; }
    .max-w-sm { max-width: 380px; }

    /* Core Card Layout Design elements Architecture */
    .order-history-card {
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .order-history-card:hover {
        border-color: rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.02);
        transform: translateY(-1px);
    }
    .order-history-card:hover .order-number-txt {
        color: var(--accent-yellow) !important;
    }

    /* Icon Decoration Structures */
    .history-icon-wrapper {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    /* Item Tag pill design properties formatting */
    .item-tag-pill {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        font-size: 0.8rem;
    }

    /* Action buttons styles mapping */
    .btn-action-track {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: rgba(255, 255, 255, 0.85);
        font-weight: 600;
        font-size: 0.825rem;
    }
    .btn-action-track:hover {
        background: var(--accent-yellow);
        border-color: var(--accent-yellow);
        color: #111;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
    }

    .border-dashed { border-style: dashed !important; }

    /* Custom App Pagination elements override styling mapping */
    .custom-pagination-wrapper .pagination {
        margin-bottom: 0;
        gap: 4px;
    }
    .custom-pagination-wrapper .page-link {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        color: #fff !important;
        border-radius: 8px;
    }
    .custom-pagination-wrapper .page-item.active .page-link {
        background: var(--accent-yellow) !important;
        border-color: var(--accent-yellow) !important;
        color: #111 !important;
    }

    /* Mobile Adaptability Responsive Layout rules adjustments */
    @media (max-width: 767.98px) {
        .w-100-mobile { width: 100% !important; margin-top: 0.5rem; text-align: center; }
        .item-pill-container { padding: 0.25rem 0; }
    }
</style>
@endpush