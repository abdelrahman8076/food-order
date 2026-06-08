@if($order->delivery_city || $order->delivery_address)
    <div class="{{ $class ?? '' }}">
        @if($order->delivery_city)
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ rtrim(__('checkout.city'), ' *') }}:</strong> {{ $order->delivery_city }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ rtrim(__('checkout.area'), ' *') }}:</strong> {{ $order->delivery_area }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ rtrim(__('checkout.street'), ' *') }}:</strong> {{ $order->delivery_street }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ rtrim(__('checkout.building'), ' *') }}:</strong> {{ $order->delivery_building }}</p>
            @if($order->delivery_floor)
                <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ __('checkout.floor') }}:</strong> {{ $order->delivery_floor }}</p>
            @endif
            @if($order->delivery_apartment)
                <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ __('checkout.apartment') }}:</strong> {{ $order->delivery_apartment }}</p>
            @endif
        @elseif($order->delivery_address)
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">{{ __('checkout.delivery_address') }}:</strong> {{ $order->delivery_address }}</p>
        @endif
    </div>
@endif
