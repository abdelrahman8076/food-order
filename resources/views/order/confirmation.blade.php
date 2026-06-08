@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<div class="container">
    <x-glass-card>
        <div class="text-center mb-4">
            <i class="bi bi-check-circle-fill fs-1" style="color: var(--primary-orange);"></i>
            <h2 class="text-white mt-3">Order Placed!</h2>
            <p class="text-white-50">Your order number is <strong class="text-white">{{ $order->order_number }}</strong></p>
        </div>

        <div class="text-center mb-3">
            <span class="badge rounded-pill px-3 py-2" id="statusBadge"
                  style="background: var(--primary-orange);">
                {{ \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status) }}
            </span>
        </div>

        <x-status-timeline :order="$order" />

        <hr style="border-color: var(--glass-border);">

        <h6 class="text-white">Order Summary</h6>
        <ul class="list-unstyled text-white-50">
            @foreach($order->orderItems as $oi)
                <li class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>{{ $oi->item_name }} x {{ $oi->quantity }}</span>
                        <span>${{ number_format($oi->total, 2) }}</span>
                    </div>
                    @if($oi->notes)
                        <div class="small ps-2"><i class="bi bi-chat-left-text me-1"></i>{{ $oi->notes }}</div>
                    @endif
                </li>
            @endforeach
        </ul>
        <div class="text-white-50 small">
            <div class="d-flex justify-content-between"><span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
            @if($order->discount > 0)
                <div class="d-flex justify-content-between text-success">
                    <span>Discount ({{ $order->coupon_code }})</span>
                    <span>-${{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="d-flex justify-content-between"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
            <div class="d-flex justify-content-between"><span>Delivery</span><span>${{ number_format($order->delivery_fee, 2) }}</span></div>
        </div>
        <p class="text-white fw-bold mt-2 mb-0">Total: ${{ number_format($order->total, 2) }}</p>

        <h6 class="text-white mt-3">Delivery Address</h6>
        <x-delivery-address :order="$order" line-class="text-white-50" label-class="text-white" />

        <p class="small text-white-50 mt-3">
            <i class="bi bi-cash me-1"></i> Pay with cash on delivery
        </p>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <a href="{{ route('order.track', ['order_number' => $order->order_number, 'token' => $order->tracking_token]) }}" class="btn btn-primary-orange">
                <i class="bi bi-geo-alt me-1"></i> Track Order
            </a>
            <a href="{{ route('menu.index') }}" class="btn btn-outline-glass">Order More</a>
            @if($settings->store_phone)
                @php
                    $waText = urlencode("Hi! I placed order {$order->order_number} for \${$order->total}. Thanks!");
                @endphp
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings->store_phone) }}?text={{ $waText }}"
                   target="_blank" class="btn btn-outline-glass">
                    <i class="bi bi-whatsapp me-1"></i> Message Cook
                </a>
            @endif
        </div>
    </x-glass-card>
</div>

@if(!in_array($order->status, ['delivered', 'cancelled']))
    @include('order._status-poll', [
        'orderId' => $order->id,
        'initialStatus' => $order->status,
        'trackingToken' => $order->tracking_token,
    ])
@endif
@endsection
