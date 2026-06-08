@if($order->delivery_city || $order->delivery_address)
    <div class="{{ $class ?? '' }}">
        @if($order->delivery_city)
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">City:</strong> {{ $order->delivery_city }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Area:</strong> {{ $order->delivery_area }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Street:</strong> {{ $order->delivery_street }}</p>
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Building:</strong> {{ $order->delivery_building }}</p>
            @if($order->delivery_floor)
                <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Floor:</strong> {{ $order->delivery_floor }}</p>
            @endif
            @if($order->delivery_apartment)
                <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Apartment:</strong> {{ $order->delivery_apartment }}</p>
            @endif
        @elseif($order->delivery_address)
            <p class="mb-1 {{ $lineClass ?? '' }}"><strong class="{{ $labelClass ?? '' }}">Address:</strong> {{ $order->delivery_address }}</p>
        @endif
    </div>
@endif
