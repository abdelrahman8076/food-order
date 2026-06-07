@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<div class="container">
    <div class="card border-success">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-check-circle me-2"></i>Order Placed Successfully</h4>
        </div>
        <div class="card-body">
            <p class="lead">Thank you! Your order number is <strong>{{ $order->order_number }}</strong>.</p>
            <p class="text-muted">We'll prepare your order and update the status. You can track it below.</p>
            <hr>
            <h6>Order summary</h6>
            <ul class="list-unstyled">
                @foreach($order->orderItems as $oi)
                    <li>{{ $oi->item_name }} x {{ $oi->quantity }} — ${{ number_format($oi->total, 2) }}</li>
                @endforeach
            </ul>
            <p><strong>Total: ${{ number_format($order->total, 2) }}</strong></p>
            <a href="{{ route('order.track') }}?order_number={{ $order->order_number }}" class="btn btn-primary">Track this order</a>
            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">Order more</a>
        </div>
    </div>
</div>
@endsection
