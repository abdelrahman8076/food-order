@props(['item'])

<div class="card food-card" id="item-{{ $item->id }}">
    <a href="{{ route('menu.item', $item->slug) }}" class="food-card-link text-decoration-none">
        <div class="food-header">
            <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}" loading="lazy">
            @unless($item->is_available)
                <div class="sold-out-overlay">Sold Out</div>
            @endunless
        </div>
        <div class="card-body d-flex flex-column pb-0">
            <h6 class="food-title text-truncate mb-1">{{ $item->name }}</h6>
            <p class="food-desc mb-0">{{ $item->description ?: 'Homemade with love.' }}</p>
        </div>
    </a>
    <div class="card-body pt-2 d-flex justify-content-between align-items-center">
        <span class="price-tag">${{ number_format($item->price, 2) }}</span>
        @if($item->is_available)
            <button type="button" class="btn-add" onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $item->id }}, this)">
                <i class="bi bi-plus-lg"></i>
            </button>
        @else
            <button type="button" class="btn-add" disabled style="opacity:0.4;">
                <i class="bi bi-x-lg"></i>
            </button>
        @endif
    </div>
</div>
