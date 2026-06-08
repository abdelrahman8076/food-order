@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<h1 class="mb-4">Kitchen Board</h1>

<div class="row g-3 mb-5" id="kitchenBoardColumns">
    @include('admin.orders._kitchen-board-columns')
</div>

@include('admin.orders._kitchen-board-poll')

<h2 class="h5 mb-3">All Orders</h2>
<form class="row g-2 mb-3">
    <div class="col-auto">
        <input type="text" name="order_number" class="form-control form-control-sm" placeholder="Order number" value="{{ request('order_number') }}">
    </div>
    <div class="col-auto">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach(array_merge(\App\Services\OrderService::STATUSES, ['cancelled' => 'Cancelled']) as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-sm btn-admin-primary">Filter</button>
    </div>
</form>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 admin-table">
            <thead>
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
                        <td><span class="badge admin-badge">{{ \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status) }}</span></td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->created_at->format('M d, H:i') }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-admin-outline">View</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
@endsection
