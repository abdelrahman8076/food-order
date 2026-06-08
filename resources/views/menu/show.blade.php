@extends('layouts.app')

@section('title', $item->name)

@push('styles')
<style>
    .item-hero {
        position: relative;
        aspect-ratio: 16 / 10;
        border-radius: 20px;
        overflow: hidden;
        background: rgba(0, 0, 0, 0.2);
    }

    .item-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .item-category-badge {
        display: inline-block;
        background: rgba(255, 190, 118, 0.15);
        color: var(--accent-yellow);
        border: 1px solid rgba(255, 190, 118, 0.3);
        border-radius: 50px;
        padding: 0.25rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
    }

    .item-category-badge:hover {
        color: var(--accent-yellow);
        border-color: var(--accent-yellow);
    }

    .item-price {
        color: var(--accent-yellow);
        font-weight: 800;
        font-size: 1.75rem;
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .qty-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid var(--glass-border);
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
    }

    .qty-btn:hover { border-color: var(--primary-orange); color: var(--primary-orange); }

    .qty-value {
        width: 50px;
        text-align: center;
        font-weight: 700;
        font-size: 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-glass">
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            @if($item->category)
                <li class="breadcrumb-item"><a href="{{ route('menu.category', $item->category->slug) }}">{{ $item->category->name }}</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $item->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="item-hero">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->name }}">
                @unless($item->is_available)
                    <div class="sold-out-overlay">Sold Out</div>
                @endunless
            </div>
        </div>

        <div class="col-lg-6">
            <x-glass-card>
                @if($item->category)
                    <a href="{{ route('menu.category', $item->category->slug) }}" class="item-category-badge mb-3">
                        {{ $item->category->name }}
                    </a>
                @endif

                <h1 class="text-white fw-bold mb-3">{{ $item->name }}</h1>
                <p class="text-white-50 mb-4">{{ $item->description ?: 'Homemade with love, prepared fresh for you.' }}</p>

                <div class="item-price mb-4">${{ number_format($item->price, 2) }}</div>

                @if($item->is_available)
                    <form action="{{ route('cart.add', $item) }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="quantity" id="quantityInput" value="1">

                        <div class="qty-control mb-3">
                            <button type="button" class="qty-btn" id="qtyMinus" aria-label="Decrease quantity">−</button>
                            <span class="qty-value" id="qtyDisplay">1</span>
                            <button type="button" class="qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white-50" for="itemNotes">Special request (optional)</label>
                            <input type="text" name="notes" id="itemNotes" class="form-control glass-input"
                                   maxlength="50" placeholder="No onions, extra sauce..."
                                   value="{{ old('notes') }}">
                            <div class="d-flex justify-content-end mt-1">
                                <small class="text-white-50"><span id="notesCount">0</span>/50</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-orange w-100 py-3">
                            <i class="bi bi-bag-plus me-2"></i>Add to Cart
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-outline-glass w-100 py-3" disabled>
                        Currently Unavailable
                    </button>
                @endif

                <a href="{{ $item->category ? route('menu.category', $item->category->slug) : route('menu.index') }}"
                   class="btn btn-outline-glass w-100 mt-2">
                    Back to Menu
                </a>
            </x-glass-card>
        </div>
    </div>

    @if($related->isNotEmpty())
    <div class="mt-5">
        <h2 class="h4 fw-bold mb-4" style="color: var(--accent-yellow);">You May Also Like</h2>
        <div class="row g-3 g-md-4">
            @foreach($related as $relatedItem)
            <div class="col-6 col-md-4 col-lg-3">
                <x-food-card :item="$relatedItem" />
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@if($item->is_available)
@push('scripts')
<script>
(function() {
    let qty = 1;
    const min = 1, max = 20;
    const display = document.getElementById('qtyDisplay');
    const input = document.getElementById('quantityInput');

    document.getElementById('qtyMinus').addEventListener('click', function() {
        if (qty > min) { qty--; display.textContent = qty; input.value = qty; }
    });
    document.getElementById('qtyPlus').addEventListener('click', function() {
        if (qty < max) { qty++; display.textContent = qty; input.value = qty; }
    });

    const notesInput = document.getElementById('itemNotes');
    const notesCount = document.getElementById('notesCount');
    if (notesInput && notesCount) {
        const updateCount = () => { notesCount.textContent = notesInput.value.length; };
        notesInput.addEventListener('input', updateCount);
        updateCount();
    }
})();
</script>
@endpush
@endif
@endsection
