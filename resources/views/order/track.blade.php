@extends('layouts.app')

@section('title', __('orders.track_title'))

@section('content')
<div class="container py-4 py-md-5">
    
    <!-- Page Header Context -->
    <div class="mb-4">
        <span class="text-uppercase tracking-widest small text-warning fw-bold mb-1 d-block">{{ __('orders.live_updates') }}</span>
        <h1 class="display-5 fw-black text-white m-0">{{ __('orders.track_heading') }} <span class="italic-serif text-accent-yellow">{{ __('orders.track_heading_accent') }}</span></h1>
        <p class="text-white-50 small mt-1 mb-0">{{ __('orders.track_subtext') }}</p>
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
                           placeholder="{{ __('orders.phone_placeholder') }}"
                           value="{{ request('phone', $searchedPhone ?? '') }}" required>
                </div>
            </div>
            @if(request('token'))
                <input type="hidden" name="token" value="{{ request('token') }}">
                <input type="hidden" name="order_number" value="{{ request('order_number') }}">
            @endif
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary-orange w-100 py-25 fw-bold text-uppercase tracking-wider rounded-3 shadow-sm">
                    <i class="bi bi-search me-2 small"></i>{{ __('orders.find_orders') }}
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
                <h6 class="text-white-50 small text-uppercase tracking-wider m-0 fw-semibold">{{ __('orders.list_heading') }}</h6>
                <span class="badge bg-dark border border-secondary border-opacity-25 text-white-50 fw-normal">
                    {{ $orders->count() }} {{ $orders->count() === 1 ? __('orders.record') : __('orders.records') }}
                </span>
            </div>

            <!-- List of orders -->
            <div class="d-flex flex-column gap-3 mb-4" id="trackOrderList">
                @foreach($orders as $listedOrder)
                    @php
                        $isListedActive = isset($activeOrder) && $activeOrder && $listedOrder->id === $activeOrder->id;
                        $isSelected = $initialExpandedId === $listedOrder->id;
                        $isInProgress = !in_array($listedOrder->status, ['delivered', 'cancelled']);
                        
                        $statusVariant = match($listedOrder->status) {
                            'pending' => 'warning',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'processing', 'preparing', 'confirmed', 'ready', 'out_for_delivery' => 'info',
                            default => 'primary',
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
                                            <span>{{ $listedOrder->created_at->locale(app()->getLocale())->translatedFormat('M d, Y · g:i A') }}</span>
                                            @if($isListedActive)
                                                <span class="pulse-indicator-dot"></span>
                                                <span class="text-accent-yellow fw-medium">{{ __('orders.active_tracking') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 text-sm-end d-flex justify-content-between justify-content-sm-end align-items-center gap-3">
                                    <div class="order-financial-meta">
                                        <x-public.status-pill
                                            :variant="$statusVariant"
                                            class="mb-1"
                                            id="statusBadge-{{ $listedOrder->id }}">
                                            {{ __('orders.statuses.' . $listedOrder->status) }}
                                        </x-public.status-pill>
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
                        {{ __('orders.empty_phone') }}
                    @elseif($searchedOrderNumber)
                        {!! __('orders.empty_order_number', ['order_number' => '<strong class="text-warning font-serif">' . e($searchedOrderNumber) . '</strong>']) !!}
                    @else
                        {{ __('orders.empty_generic') }}
                    @endif
                </p>
                <a href="{{ route('menu.index') }}" class="btn btn-sm btn-outline-warning mt-3 px-4 rounded-pill">{{ __('orders.explore_menu') }}</a>
            </x-glass-card>
        @endif
    @endif
</div>
@endsection
