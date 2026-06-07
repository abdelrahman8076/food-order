@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
    </ol>
</nav>
<h1 class="mb-4">Order {{ $order->order_number }}</h1>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">Customer & delivery</div>
            <div class="card-body">
                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)<p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>@endif
                <p class="mb-1"><strong>Order type:</strong> {{ ucfirst($order->order_type) }}</p>
                @if($order->delivery_address)<p class="mb-1"><strong>Address:</strong> {{ $order->delivery_address }}</p>@endif
                @if($order->table_number)<p class="mb-1"><strong>Table:</strong> {{ $order->table_number }}</p>@endif
                @if($order->notes)<p class="mb-0"><strong>Notes:</strong> {{ $order->notes }}</p>@endif
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Items</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($order->orderItems as $oi)
                            <tr>
                                <td>{{ $oi->item_name }}</td>
                                <td>{{ $oi->quantity }}</td>
                                <td>${{ number_format($oi->unit_price, 2) }}</td>
                                <td>${{ number_format($oi->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <p class="mb-0">Subtotal: ${{ number_format($order->subtotal, 2) }} | Tax: ${{ number_format($order->tax, 2) }} | Delivery: ${{ number_format($order->delivery_fee, 2) }}</p>
                <p class="fw-bold fs-5 mb-0">Total: ${{ number_format($order->total, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Update status</div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
                <p class="small text-muted mt-2 mb-0">Placed: {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
