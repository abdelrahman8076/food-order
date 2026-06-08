@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container">
    <h1 class="page-heading mb-4">My <span>Orders</span></h1>

    @forelse($orders as $order)
        <x-glass-card class="mb-3">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="text-white mb-1">{{ $order->order_number }}</h6>
                    <span class="text-white-50 small">{{ $order->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <span class="badge rounded-pill px-3 py-2" style="background: var(--primary-orange);">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <ul class="list-unstyled text-white-50 small mb-2">
                @foreach($order->orderItems as $oi)
                    <li>{{ $oi->item_name }} x {{ $oi->quantity }}</li>
                @endforeach
            </ul>
            <div class="d-flex justify-content-between align-items-center">
                <span class="price-tag">${{ number_format($order->total, 2) }}</span>
                <a href="{{ route('order.track') }}?order_number={{ $order->order_number }}" class="btn btn-sm btn-outline-glass">Track</a>
            </div>
        </x-glass-card>
    @empty
        <x-glass-card>
            <p class="text-white-50 mb-3">You haven't placed any orders yet.</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary-orange">Browse Menu</a>
        </x-glass-card>
    @endforelse

    <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection
