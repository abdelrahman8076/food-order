@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<h1 class="mb-4">Orders</h1>
<form class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="order_number" class="form-control" placeholder="Order number" value="{{ request('order_number') }}">
    </div>
    <div class="col-auto">
        <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
            <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->customer_phone }}</td>
                            <td>{{ ucfirst($order->order_type) }}</td>
                            <td><span class="badge bg-{{ $order->status === 'cancelled' ? 'danger' : ($order->status === 'delivered' ? 'success' : 'warning') }}">{{ ucfirst($order->status) }}</span></td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
