@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container">
    <h1 class="mb-4">Track Your Order</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('order.track') }}" method="GET" class="row g-2">
                <div class="col-auto flex-grow-1">
                    <input type="text" name="order_number" class="form-control" placeholder="Enter order number (e.g. ORD202502030001)" value="{{ request('order_number') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Track</button>
                </div>
            </form>
        </div>
    </div>

    @if(request()->has('order_number'))
        @if($order)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Order {{ $order->order_number }}</strong>
                    <span class="badge bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'delivered' ? 'success' : 'warning') }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                    <p class="mb-1"><strong>Type:</strong> {{ ucfirst($order->order_type) }}</p>
                    <p class="mb-2"><strong>Placed:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    <hr>
                    <h6>Items</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach($order->orderItems as $oi)
                            <li>{{ $oi->item_name }} x {{ $oi->quantity }} — ${{ number_format($oi->total, 2) }}</li>
                        @endforeach
                    </ul>
                    <hr>
                    <p class="mb-0"><strong>Total: ${{ number_format($order->total, 2) }}</strong></p>
                </div>
            </div>
        @else
            <div class="alert alert-warning">No order found with that order number.</div>
        @endif
    @endif
</div>
@endsection
