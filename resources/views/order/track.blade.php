@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container">
    <h1 class="page-heading mb-4">Track <span>Order</span></h1>

    @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <x-glass-card class="mb-4">
        <form action="{{ route('order.track') }}" method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="tel" name="phone" class="form-control glass-input"
                       placeholder="Phone number used at checkout"
                       value="{{ request('phone', $searchedPhone ?? '') }}" required>
            </div>
            @if(request('token'))
                <input type="hidden" name="token" value="{{ request('token') }}">
                <input type="hidden" name="order_number" value="{{ request('order_number') }}">
            @endif
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary-orange w-100">Find My Orders</button>
            </div>
        </form>
    </x-glass-card>

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

            <h6 class="text-white mb-3">Your Orders (last 15 days)</h6>
            <div class="d-flex flex-column gap-2 mb-4" id="trackOrderList">
                @foreach($orders as $listedOrder)
                    @php
                        $isListedActive = isset($activeOrder) && $activeOrder && $listedOrder->id === $activeOrder->id;
                        $isSelected = $initialExpandedId === $listedOrder->id;
                        $isInProgress = !in_array($listedOrder->status, ['delivered', 'cancelled']);
                    @endphp
                    <x-glass-card
                        class="track-order-card {{ $isSelected ? 'border border-warning border-opacity-50' : '' }}"
                        data-order-id="{{ $listedOrder->id }}"
                        data-tracking-token="{{ $listedOrder->tracking_token }}"
                        data-status="{{ $listedOrder->status }}"
                        data-in-progress="{{ $isInProgress ? '1' : '0' }}">
                        <button type="button"
                                class="track-order-row w-100"
                                data-order-id="{{ $listedOrder->id }}"
                                aria-expanded="{{ $isSelected ? 'true' : 'false' }}">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="text-white fw-semibold track-order-number">{{ $listedOrder->order_number }}</div>
                                    <div class="text-white-50 small">
                                        {{ $listedOrder->created_at->format('M d, Y g:i A') }}
                                        @if($isListedActive)
                                            · <span class="text-warning">In progress</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge rounded-pill px-3 py-2 mb-2 d-inline-block"
                                          id="statusBadge-{{ $listedOrder->id }}"
                                          style="background: var(--primary-orange);">
                                        {{ \App\Services\OrderService::STATUSES[$listedOrder->status] ?? ucfirst($listedOrder->status) }}
                                    </span>
                                    <div class="text-white-50 small">${{ number_format($listedOrder->total, 2) }}</div>
                                </div>
                                <i class="bi track-order-chevron {{ $isSelected ? 'bi-chevron-up' : 'bi-chevron-down' }} text-white-50 flex-shrink-0"></i>
                            </div>
                        </button>
                        <div class="track-order-body mt-3 {{ $isSelected ? '' : 'd-none' }}"
                             data-order-id="{{ $listedOrder->id }}">
                            @include('order._track-detail', ['order' => $listedOrder, 'embedded' => true])
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

                        card.classList.toggle('border', isOpen);
                        card.classList.toggle('border-warning', isOpen);
                        card.classList.toggle('border-opacity-50', isOpen);

                        const body = card.querySelector('.track-order-body');
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
            @include('order._track-detail', ['order' => $order])

            @if(!in_array($order->status, ['delivered', 'cancelled']))
                @include('order._status-poll', [
                    'orderId' => $order->id,
                    'initialStatus' => $order->status,
                    'trackingToken' => $trackingToken ?? null,
                ])
            @endif
        @else
            <x-glass-card>
                <p class="text-white-50 mb-0">
                    @if($searchedPhone)
                        No orders found for this phone number in the last 15 days.
                    @elseif($searchedOrderNumber)
                        No order found for <strong class="text-white">{{ $searchedOrderNumber }}</strong>.
                    @else
                        No orders found.
                    @endif
                </p>
            </x-glass-card>
        @endif
    @endif
</div>
@endsection

@push('styles')
<style>
    .track-order-row {
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        color: inherit;
        text-align: left;
    }
    .track-order-row:hover .track-order-number {
        color: var(--primary-orange) !important;
    }
</style>
@endpush
