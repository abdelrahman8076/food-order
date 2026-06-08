@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container py-4 py-md-5">
    
    <!-- Page Header Context -->
    <div class="mb-4">
        <span class="text-uppercase tracking-widest small text-warning fw-bold mb-1 d-block">Live Updates</span>
        <h1 class="display-5 fw-black text-white m-0">Track <span class="italic-serif text-accent-yellow">Order</span></h1>
        <p class="text-white-50 small mt-1 mb-0">Monitor your fresh, homemade meals on their way to you.</p>
    </div>

    @if(session('error'))
        <div class="alert alert-custom-danger d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Search Portal Block -->
    <x-glass-card class="mb-5 tracking-search-box p-3 p-md-4">
        <form action="{{ route('order.track') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8">
                <div class="input-icon-group position-relative">
                    <i class="bi bi-telephone text-white-50 position-absolute top-50 start-0 translate-middle-y ms-3 fs-5"></i>
                    <input type="tel" name="phone" class="form-control tracking-glass-input ps-5"
                           placeholder="Phone number used at checkout"
                           value="{{ request('phone', $searchedPhone ?? '') }}" required>
                </div>
            </div>
            @if(request('token'))
                <input type="hidden" name="token" value="{{ request('token') }}">
                <input type="hidden" name="order_number" value="{{ request('order_number') }}">
            @endif
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary-orange w-100 py-25 fw-bold text-uppercase tracking-wider rounded-3 shadow-sm">
                    <i class="bi bi-search me-2 small"></i>Find My Orders
                </button>
            </div>
        </form>
    </x-glass-card>

    <!-- Query Results Output Handling -->
    @if($searched ?? false)
        @if(($orders ?? collect())->isNotEmpty() && $order)
            @php
                $initialExpandedId = $order->id;
                $pollOrders = $orders
                    ->filter(fn ($o) => !in_array($o->status, ['delivered', 'cancelled']))
                    ->map(fn ($o) => [
                        'id' => $o->id,
                        'status' => $o->status,
                        'token' => $o->tracking_token,
                    ])
                    ->values();
            @endphp

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="text-white-50 small text-uppercase tracking-wider m-0 fw-semibold">Your Orders (Last 15 Days)</h6>
                <span class="badge bg-dark border border-secondary border-opacity-25 text-white-50 fw-normal">
                    {{ $orders->count() }} {{ Str::plural('Record', $orders->count()) }}
                </span>
            </div>

            <!-- List of orders -->
            <div class="d-flex flex-column gap-3 mb-4" id="trackOrderList">
                @foreach($orders as $listedOrder)
                    @php
                        $isListedActive = isset($activeOrder) && $activeOrder && $listedOrder->id === $activeOrder->id;
                        $isSelected = $initialExpandedId === $listedOrder->id;
                        $isInProgress = !in_array($listedOrder->status, ['delivered', 'cancelled']);
                        
                        // Map status codes cleanly to semantic style sets
                        $statusStyles = match($listedOrder->status) {
                            'pending' => ['bg' => 'rgba(255, 193, 7, 0.15)', 'color' => '#ffc107', 'border' => 'rgba(255,193,7,0.3)'],
                            'processing', 'preparing' => ['bg' => 'rgba(13, 202, 240, 0.15)', 'color' => '#0dcaf0', 'border' => 'rgba(13,202,240,0.3)'],
                            'delivered' => ['bg' => 'rgba(25, 135, 84, 0.15)', 'color' => '#198754', 'border' => 'rgba(25,135,84,0.3)'],
                            'cancelled' => ['bg' => 'rgba(220, 53, 69, 0.15)', 'color' => '#dc3545', 'border' => 'rgba(220,53,69,0.3)'],
                            default => ['bg' => 'rgba(255, 127, 80, 0.15)', 'color' => 'var(--primary-orange)', 'border' => 'rgba(255,127,80,0.3)']
                        };
                    @endphp
                    
                    <x-glass-card
                        class="track-order-card transition-all {{ $isSelected ? 'active-expanded-card' : '' }}"
                        data-order-id="{{ $listedOrder->id }}"
                        data-tracking-token="{{ $listedOrder->tracking_token }}"
                        data-status="{{ $listedOrder->status }}"
                        data-in-progress="{{ $isInProgress ? '1' : '0' }}">
                        
                        <button type="button"
                                class="track-order-row w-100 py-1"
                                data-order-id="{{ $listedOrder->id }}"
                                aria-expanded="{{ $isSelected ? 'true' : 'false' }}">
                            <div class="row align-items-center g-3">
                                <div class="col-sm-6 d-flex align-items-center gap-3">
                                    <div class="order-icon-hex d-none d-md-flex">
                                        <i class="bi bi-box-seam text-accent-yellow"></i>
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold tracking-tight fs-5 mb-1 track-order-number">
                                            {{ $listedOrder->order_number }}
                                        </div>
                                        <div class="text-white-50 small d-flex flex-wrap align-items-center gap-2">
                                            <span>{{ $listedOrder->created_at->format('M d, Y · g:i A') }}</span>
                                            @if($isListedActive)
                                                <span class="pulse-indicator-dot"></span>
                                                <span class="text-accent-yellow fw-medium">Active Tracking</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-sm-end d-flex justify-content-between justify-content-sm-end align-items-center gap-3">
                                    <div class="order-financial-meta">
                                        <span class="badge dynamic-status-badge px-3 py-2 border mb-1 d-inline-block"
                                              id="statusBadge-{{ $listedOrder->id }}"
                                              style="background-color: {{ $statusStyles['bg'] }}; color: {{ $statusStyles['color'] }}; border-color: {{ $statusStyles['border'] }} !important;">
                                            {{ \App\Services\OrderService::STATUSES[$listedOrder->status] ?? ucfirst($listedOrder->status) }}
                                        </span>
                                        <div class="text-white font-serif small fw-bold">${{ number_format($listedOrder->total, 2) }}</div>
                                    </div>
                                    <div class="chevron-wrapper bg-white bg-opacity-5 rounded-circle p-2">
                                        <i class="bi track-order-chevron {{ $isSelected ? 'bi-chevron-up' : 'bi-chevron-down' }} text-white-50 d-block"></i>
                                    </div>
                                </div>
                            </div>
                        </button>

                        <div class="track-order-body-wrapper {{ $isSelected ? 'expanded-open' : 'd-none' }}"
                             data-order-id="{{ $listedOrder->id }}">
                            <div class="pt-4 border-top border-secondary border-opacity-25 mt-3">
                                @include('order._track-detail', ['order' => $listedOrder, 'embedded' => true])
                            </div>
                        </div>
                    </x-glass-card>
                @endforeach
            </div>

            @include('order._track-accordion-poll', ['pollOrders' => $pollOrders])

            @push('scripts')
            <script>
            (function() {
                const list = document.getElementById('trackOrderList');
                if (!list) return;

                let expandedId = {{ $initialExpandedId }};

                function setExpanded(orderId) {
                    list.querySelectorAll('.track-order-card').forEach(function(card) {
                        const id = parseInt(card.dataset.orderId, 10);
                        const isOpen = orderId !== null && id === orderId;

                        card.classList.toggle('active-expanded-card', isOpen);

                        const body = card.querySelector('.track-order-body-wrapper');
                        const btn = card.querySelector('.track-order-row');
                        const chevron = card.querySelector('.track-order-chevron');

                        if (body) body.classList.toggle('d-none', !isOpen);
                        if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                        if (chevron) {
                            chevron.classList.toggle('bi-chevron-up', isOpen);
                            chevron.classList.toggle('bi-chevron-down', !isOpen);
                        }
                    });

                    expandedId = orderId;

                    if (window.trackAccordionPoll) {
                        if (orderId === null) {
                            window.trackAccordionPoll.stop();
                        } else {
                            const card = list.querySelector('.track-order-card[data-order-id="' + orderId + '"]');
                            if (card && card.dataset.inProgress === '1') {
                                window.trackAccordionPoll.start(orderId);
                            } else {
                                window.trackAccordionPoll.stop();
                            }
                        }
                    }
                }

                list.querySelectorAll('.track-order-row').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const orderId = parseInt(btn.dataset.orderId, 10);
                        setExpanded(expandedId === orderId ? null : orderId);
                    });
                });

                setExpanded(expandedId);
            })();
            </script>
            @endpush
        @elseif($order)
            <!-- Singular Specific Order View Layout fallback -->
            <div class="mt-4">
                @include('order._track-detail', ['order' => $order])

                @if(!in_array($order->status, ['delivered', 'cancelled']))
                    @include('order._status-poll', [
                        'orderId' => $order->id,
                        'initialStatus' => $order->status,
                        'trackingToken' => $trackingToken ?? null,
                    ])
                @endif
            </div>
        @else
            <!-- Empty query results state layout block -->
            <x-glass-card class="text-center py-5 border border-dashed border-secondary border-opacity-25 rounded-4 mt-4">
                <div class="mb-3 text-muted display-6"><i class="bi bi-search-heart"></i></div>
                <p class="text-white-50 m-0 fs-6">
                    @if($searchedPhone)
                        No orders found associated with this phone number in the past 15 days.
                    @elseif($searchedOrderNumber)
                        No registration logs match order number <strong class="text-warning font-serif">{{ $searchedOrderNumber }}</strong>.
                    @else
                        No orders found matching the criteria.
                    @endif
                </p>
                <a href="{{ route('menu.index') }}" class="btn btn-sm btn-outline-warning mt-3 px-4 rounded-pill">Explore Our Kitchen Menu</a>
            </x-glass-card>
        @endif
    @endif
</div>
@endsection

@push('styles')
<style>
    /* Premium Theming Context Variables fallback code definitions */
    :root {
        --accent-yellow: #ffc107;
        --primary-orange: #ff7f50;
    }
    .fw-black { font-weight: 900; }
    .tracking-widest { letter-spacing: 2.5px; }
    .tracking-wider { letter-spacing: 1.25px; }
    .tracking-tight { letter-spacing: -0.5px; }
    .italic-serif, .font-serif { font-family: 'Playfair Display', Georgia, serif; font-style: italic; }

    /* Custom Form Architecture Details */
    .tracking-search-box {
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 18px;
    }
    .tracking-glass-input {
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: 0.8rem 1rem !important;
        transition: all 0.3s ease;
    }
    .tracking-glass-input:focus {
        background: rgba(255, 255, 255, 0.09) !important;
        border-color: var(--accent-yellow) !important;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.15) !important;
    }
    .py-25 { padding-top: 0.8rem; padding-bottom: 0.8rem; }

    /* Accordion Row Base Restyling */
    .track-order-card {
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .active-expanded-card {
        border-color: rgba(255, 193, 7, 0.4) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        background: rgba(255, 255, 255, 0.02);
    }
    .track-order-row {
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        color: inherit;
        text-align: left;
    }
    .track-order-row:focus { outline: none; }
    
    /* Interactive visual transitions hover states */
    .track-order-card:hover {
        border-color: rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.01);
    }
    .track-order-row:hover .track-order-number {
        color: var(--accent-yellow) !important;
    }

    /* Icon design elements decoration units */
    .order-icon-hex {
        width: 48px;
        height: 48px;
        background: rgba(255, 193, 7, 0.06);
        border: 1px solid rgba(255, 193, 7, 0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .chevron-wrapper {
        transition: background-color 0.2s ease;
    }
    .track-order-row:hover .chevron-wrapper {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }

    /* Active Pulse Animation Dot Element */
    .pulse-indicator-dot {
        width: 8px;
        height: 8px;
        background-color: var(--accent-yellow);
        border-radius: 50%;
        display: inline-block;
        animation: pulseAnimation 1.8s infinite ease-in-out;
    }
    @keyframes pulseAnimation {
        0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(255, 193, 7, 0); }
        100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    /* Custom Alert Elements */
    .alert-custom-danger {
        background: rgba(220, 53, 69, 0.12);
        border: 1px solid rgba(220, 53, 69, 0.25);
        color: #ea868f;
        border-radius: 12px;
        padding: 1rem;
    }
    .dynamic-status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .border-dashed { border-style: dashed !important; }
</style>
@endpush