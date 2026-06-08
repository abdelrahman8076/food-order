@foreach(['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'] as $status)
<div class="col-md-6 col-lg-4 col-xl">
    <div class="admin-board-column">
        <h6 class="admin-board-title">{{ \App\Services\OrderService::STATUSES[$status] }}</h6>
        @forelse($boardOrders->get($status, collect()) as $order)
            <div class="admin-order-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong>{{ $order->order_number }}</strong>
                    <span class="badge admin-badge">{{ ucfirst($order->order_type) }}</span>
                </div>
                <p class="small mb-1">{{ $order->customer_name }}</p>
                <p class="small text-muted mb-2">${{ number_format($order->total, 2) }}</p>
                <ul class="small mb-2 ps-3">
                    @foreach($order->orderItems as $oi)
                        <li>{{ $oi->item_name }} x{{ $oi->quantity }}</li>
                    @endforeach
                </ul>
                <div class="d-flex gap-1">
                    <form action="{{ route('admin.orders.advance', $order) }}" method="POST" class="flex-grow-1">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-admin-primary w-100">Advance</button>
                    </form>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-admin-outline">View</a>
                </div>
            </div>
        @empty
            <p class="small text-muted">No orders</p>
        @endforelse
    </div>
</div>
@endforeach
