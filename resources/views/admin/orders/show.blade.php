@extends('layouts.admin')

@section('title', __('admin.orders.title') . ' ' . $order->order_number)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">{{ __('admin.orders.title') }}</a></li>
        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
    </ol>
</nav>
<h1 class="mb-4">{{ __('admin.orders.title') }} {{ $order->order_number }}</h1>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card card mb-4">
            <div class="card-header">{{ __('admin.orders.customer_delivery') }}</div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ __('admin.orders.name') }}</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>{{ __('admin.orders.phone') }}</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)<p class="mb-1"><strong>{{ __('admin.orders.email') }}</strong> {{ $order->customer_email }}</p>@endif
                <p class="mb-1"><strong>{{ __('admin.orders.order_type') }}</strong>
                    {{ strtolower($order->order_type) === 'delivery' ? __('admin.orders.type_delivery') : __('admin.orders.type_takeout') }}
                </p>
                <x-delivery-address :order="$order" />
                @if($order->notes)<p class="mb-0"><strong>{{ __('admin.orders.notes') }}</strong> {{ $order->notes }}</p>@endif
            </div>
        </div>
        <div class="admin-card card mb-4">
            <div class="card-header">{{ __('common.items') }}</div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0 admin-table">
                    <thead><tr><th>{{ __('admin.orders.col_item') }}</th><th>{{ __('admin.orders.col_qty') }}</th><th>{{ __('admin.orders.col_notes') }}</th><th>{{ __('admin.orders.col_price') }}</th><th>{{ __('common.total') }}</th></tr></thead>
                    <tbody>
                        @foreach($order->orderItems as $oi)
                            <tr>
                                <td>{{ $oi->item_name }}</td>
                                <td>{{ $oi->quantity }}</td>
                                <td class="small text-muted">{{ $oi->notes ?: '—' }}</td>
                                <td>${{ number_format($oi->unit_price, 2) }}</td>
                                <td>${{ number_format($oi->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <p class="mb-0">
                    {{ __('common.subtotal') }}: ${{ number_format($order->subtotal, 2) }}
                    @if($order->discount > 0)
                        | {{ __('common.discount_with_code', ['code' => $order->coupon_code]) }}: -${{ number_format($order->discount, 2) }}
                    @endif
                    | {{ __('common.tax') }}: ${{ number_format($order->tax, 2) }}
                    | {{ __('common.delivery') }}: ${{ number_format($order->delivery_fee, 2) }}
                </p>
                <p class="fw-bold fs-5 mb-0">{{ __('common.total') }}: ${{ number_format($order->total, 2) }}</p>
            </div>
        </div>

        @if($order->statusLogs->isNotEmpty())
        <div class="admin-card card">
            <div class="card-header">{{ __('admin.orders.status_history') }}</div>
            <div class="card-body">
                @foreach($order->statusLogs as $log)
                    <div class="d-flex justify-content-between small mb-2">
                        <span><strong>{{ \App\Services\OrderService::statusLabel($log->status) }}</strong> @if($log->note)<span class="text-muted">— {{ $log->note }}</span>@endif</span>
                        <span class="text-muted">{{ $log->created_at->locale(app()->getLocale())->translatedFormat('M d, g:i A') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    <div class="col-lg-4">
        <div class="admin-card card mb-3">
            <div class="card-header">{{ __('admin.orders.quick_actions') }}</div>
            <div class="card-body">
                @if(!in_array($order->status, ['delivered', 'cancelled']))
                    <form action="{{ route('admin.orders.advance', $order) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-admin-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> {{ __('admin.orders.advance_stage') }}
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.orders.set_status') }}</label>
                        <select name="status" class="form-select">
                            @foreach(\App\Services\OrderService::STATUS_KEYS as $key)
                                <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ \App\Services\OrderService::statusLabel($key) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-admin-outline w-100">{{ __('admin.orders.update_status') }}</button>
                </form>
                <form action="{{ route('admin.orders.delivery-fee', $order) }}" method="POST" id="deliveryFeeForm">
                    @csrf
                    @method('PATCH')
                    <div class="mb-2">
                        <label class="form-label" for="delivery_fee">{{ __('admin.orders.delivery_fee') }}</label>
                        <input type="number" name="delivery_fee" id="delivery_fee" class="form-control"
                               value="{{ number_format($order->delivery_fee, 2, '.', '') }}" step="0.01" min="0" required>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="free_delivery">
                        <label class="form-check-label" for="free_delivery">{{ __('admin.orders.free_delivery') }}</label>
                    </div>
                    <button type="submit" class="btn btn-admin-outline w-100">{{ __('admin.orders.save_delivery_fee') }}</button>
                </form>
                <p class="small text-muted mt-2 mb-0">{{ __('admin.orders.placed') }} {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function() {
    const freeCheckbox = document.getElementById('free_delivery');
    const feeInput = document.getElementById('delivery_fee');
    if (!freeCheckbox || !feeInput) return;

    freeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            feeInput.value = '0.00';
        }
    });

    feeInput.addEventListener('input', function() {
        freeCheckbox.checked = parseFloat(this.value) === 0;
    });

    freeCheckbox.checked = parseFloat(feeInput.value) === 0;
})();
</script>
@endpush
@endsection
