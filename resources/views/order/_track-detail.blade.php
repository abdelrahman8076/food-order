@php
    $embedded = $embedded ?? false;
@endphp

@if($embedded)
    <div class="order-track-detail" data-order-id="{{ $order->id }}">
        <x-status-timeline :order="$order" :timeline-id="'statusTimeline-' . $order->id" />

        <hr style="border-color: var(--glass-border);">
        <div class="row">
            <div class="col-12">
                <h6 class="text-white">Items</h6>
                <ul class="list-unstyled text-white-50 small">
                    @foreach($order->orderItems as $oi)
                        <li class="mb-1">
                            {{ $oi->item_name }} x {{ $oi->quantity }} — ${{ number_format($oi->total, 2) }}
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
                            <span>Discount</span>
                            <span>-${{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Delivery</span><span>${{ number_format($order->delivery_fee, 2) }}</span></div>
                </div>
                <p class="text-white fw-bold mb-0 mt-2">Total: ${{ number_format($order->total, 2) }}</p>
            </div>
        </div>
    </div>
@else
    <x-glass-card id="orderTrackCard" data-order-id="{{ $order->id }}">
        @if(!empty($heading))
            <h6 class="text-white mb-3">{{ $heading }}</h6>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="text-white mb-1">{{ $order->order_number }}</h5>
                <span class="text-white-50 small">{{ $order->created_at->format('M d, Y g:i A') }}</span>
            </div>
            <span class="badge rounded-pill px-3 py-2" id="statusBadge"
                  style="background: var(--primary-orange);">{{ \App\Services\OrderService::STATUSES[$order->status] ?? ucfirst($order->status) }}</span>
        </div>

        <x-status-timeline :order="$order" />

        <hr style="border-color: var(--glass-border);">
        <div class="row">
            <div class="col-12">
                <h6 class="text-white">Items</h6>
                <ul class="list-unstyled text-white-50 small">
                    @foreach($order->orderItems as $oi)
                        <li class="mb-1">
                            {{ $oi->item_name }} x {{ $oi->quantity }} — ${{ number_format($oi->total, 2) }}
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
                            <span>Discount</span>
                            <span>-${{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                    <div class="d-flex justify-content-between"><span>Delivery</span><span>${{ number_format($order->delivery_fee, 2) }}</span></div>
                </div>
                <p class="text-white fw-bold mb-0 mt-2">Total: ${{ number_format($order->total, 2) }}</p>
            </div>
        </div>
    </x-glass-card>
@endif
