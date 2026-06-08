@extends('layouts.app')

@section('title', $item->localizedName())
@section('content')
<div class="container py-4">
    <!-- Breadcrumbs structured minimalistically -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb m-0 small tracking-wider text-uppercase">
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}" class="text-white-50 text-decoration-none">{{ __('menu.breadcrumb') }}</a></li>
            @if($item->category)
                <li class="breadcrumb-item"><a href="{{ route('menu.category', $item->category->slug) }}" class="text-white-50 text-decoration-none">{{ $item->category->localizedName() }}</a></li>
            @endif
            <li class="breadcrumb-item active text-warning" aria-current="page">{{ $item->localizedName() }}</li>
        </ol>
    </nav>

    <div class="row g-4 lg-g-5">
        <!-- Visual Hero Half -->
        <div class="col-lg-6">
            <div class="item-hero-wrapper">
                <div class="item-hero-frame">
                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->localizedName() }}">
                </div>
                @unless($item->is_available)
                    <div class="sold-out-pill">{{ __('menu.sold_out') }}</div>
                @endunless
            </div>
        </div>

        <!-- Order Parameters Half -->
        <div class="col-lg-6">
            <x-glass-card class="p-4 p-md-5 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        @if($item->category)
                            <a href="{{ route('menu.category', $item->category->slug) }}" class="item-category-badge">
                                {{ $item->category->localizedName() }}
                            </a>
                        @endif
                        <div class="item-price-tag">${{ number_format($item->price, 2) }}</div>
                    </div>

                    <h1 class="text-white fw-black tracking-tight mb-3 display-6">{{ $item->localizedName() }}</h1>
                    <p class="text-white-50 mb-4 lh-base fs-6">{{ $item->localizedDescription() ?: __('menu.fallback_description') }}</p>
                    
                    <hr class="border-secondary opacity-25 my-4">

                    @if($item->is_available)
                        <form action="{{ route('cart.add', $item) }}" method="POST" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="quantity" id="quantityInput" value="1">

                            <!-- Modernized Counter Module -->
                            <div class="mb-4 d-flex align-items-center gap-3">
                                <span class="small text-white-50 fw-semibold text-uppercase tracking-wider">{{ __('menu.select_quantity') }}</span>
                                <div class="qty-control-panel">
                                    <button type="button" class="qty-btn" id="qtyMinus" aria-label="{{ __('menu.qty_decrease') }}">−</button>
                                    <span class="qty-value" id="qtyDisplay">1</span>
                                    <button type="button" class="qty-btn" id="qtyPlus" aria-label="{{ __('menu.qty_increase') }}">+</button>
                                </div>
                            </div>

                            <!-- Styled Request Box -->
                            <div class="mb-4">
                                <label class="form-label text-white-50 small text-uppercase tracking-wider fw-semibold mb-2" for="itemNotes">
                                    {{ __('menu.special_instructions') }}
                                </label>
                                <input type="text" name="notes" id="itemNotes" class="form-control custom-glass-input"
                                       maxlength="50" placeholder="{{ __('menu.notes_placeholder') }}"
                                       value="{{ old('notes') }}">
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-white-50 opacity-50 small" id="notesCounterLabel">{{ __('menu.notes_counter', ['count' => 0]) }}</small>
                                </div>
                            </div>
                    @endif
                </div>

                <!-- Footer Placement Action Triggers -->
                <div class="mt-auto">
                    @if($item->is_available)
                        <button type="submit" class="btn btn-primary-orange w-100 py-3 fw-bold text-uppercase tracking-wider rounded-3 shadow">
                            {{ __('menu.add_to_cart') }}
                        </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-outline-secondary w-100 py-3 text-uppercase tracking-wider rounded-3" disabled>
                            {{ __('menu.out_of_stock') }}
                        </button>
                    @endif

                    <a href="{{ $item->category ? route('menu.category', $item->category->slug) : route('menu.index') }}"
                       class="btn btn-link text-white-50 text-decoration-none w-100 text-center btn-sm mt-3 tracking-wide">
                       {{ __('menu.back') }}
                    </a>
                </div>
            </x-glass-card>
        </div>
    </div>

    <!-- Recommendations Module -->
    @if($related->isNotEmpty())
    <div class="mt-5 pt-4">
        <div class="mb-4">
            <h2 class="h4 fw-bold text-white m-0">{{ __('menu.related_title') }}</h2>
            <div class="editorial-divider mt-2"></div>
        </div>
        <div class="row g-3 g-md-4">
            @foreach($related as $relatedItem)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="menu-card-item">
                    <x-food-card :item="$relatedItem" />
                </div>
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
    const notesCounterLabel = document.getElementById('notesCounterLabel');
    const notesCounterTemplate = @json(__('menu.notes_counter', ['count' => ':count']));
    if (notesInput && notesCounterLabel) {
        const updateCount = () => {
            notesCounterLabel.textContent = notesCounterTemplate.replace(':count', notesInput.value.length);
        };
        notesInput.addEventListener('input', updateCount);
        updateCount();
    }
})();
</script>
@endpush
@endif
@endsection
