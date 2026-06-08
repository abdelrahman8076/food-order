@extends('layouts.app')

@section('title', __('cart.title'))
@section('content')
<div class="container py-4 cart-page">
    <!-- Header Vector Line -->
    <div class="d-flex align-items-center gap-3 mb-5 border-bottom border-white border-opacity-10 pb-3">
        <div class="glass-icon-circle d-flex align-items-center justify-content-center text-primary-orange fs-3">
            <i class="bi bi-bag-heart"></i>
        </div>
        <h1 class="page-heading h2 m-0 fw-bold">
            {{ __('cart.heading') }} <span class="text-primary-orange fw-light">{{ __('cart.heading_accent') }}</span>
        </h1>
    </div>

    @if(empty($items))
        <!-- Empty Cart Display State -->
        <div class="row justify-content-center text-center py-5">
            <div class="col-md-6 col-lg-5">
                <x-glass-card class="py-5 px-4 shadow-lg border border-white border-opacity-10 rounded-4">
                    <div class="display-1 text-white border-white border-opacity-10 mb-4 opacity-25">
                        <i class="bi bi-cart-x"></i>
                    </div>
                    <h3 class="h5 fw-bold text-white mb-2">Your cart feels light!</h3>
                    <p class="text-white-50 small mb-4 mx-auto max-w-xs">{{ __('cart.empty') }}</p>
                    <a href="{{ route('menu.index') }}" class="btn btn-primary-orange px-4 py-2.5 rounded-pill fw-semibold transition-all">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('cart.browse_menu') }}
                    </a>
                </x-glass-card>
            </div>
        </div>
    @else
    <div class="row g-4 align-items-start">
        <!-- Item Matrix Section -->
        <div class="col-lg-8">
            <x-glass-card class="p-0 overflow-hidden shadow-lg border border-white border-opacity-10 rounded-4">
                <div class="p-3 bg-glass-panel border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center">
                    <span class="text-white-50 small fw-bold tracking-wider text-uppercase">Selected Selections</span>
                    <span class="badge bg-glass-panel-strong text-white rounded-pill font-monospace small px-2.5 py-1">{{ count($items) }} items</span>
                </div>
                
                <div class="divide-y-glass">
                    @foreach($items as $row)
                        <div class="cart-item position-relative transition-all" id="cart-item-{{ $row->id }}" data-item-id="{{ $row->id }}">
                            
                            <!-- Static Row Card Trigger -->
                            <div class="cart-item-row p-4 d-flex justify-content-between align-items-center gap-3 cursor-pointer user-select-none hover-bg-glass">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-1.5 flex-wrap">
                                        <h5 class="text-white fw-bold h6 m-0 truncate-text">{{ $row->name }}</h5>
                                        <span class="badge bg-primary-orange bg-opacity-20 text-primary-orange border border-primary-orange border-opacity-25 font-monospace small px-2 py-0.5">
                                            ×{{ $row->quantity }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-white-50 small">
                                        <span class="font-monospace">${{ number_format($row->price, 2) }} {{ __('cart.price_each') }}</span>
                                        @if(!empty($row->notes))
                                            <span class="text-white-30">•</span>
                                            <span class="text-truncate d-inline-block max-w-sm text-accent-cyan fs-xs"><i class="bi bi-chat-left-text me-1"></i>{{ $row->notes }}</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                    <span class="price-tag font-monospace fw-bold text-white fs-5">${{ number_format($row->total, 2) }}</span>
                                    <form action="{{ route('cart.remove', $row->id) }}" method="POST" class="d-inline cart-remove-form m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-icon-glass text-white-50 hover-text-danger" title="Remove Item">
                                            <i class="bi bi-trash3 fs-6"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Expandable Operational Editor Form Compartment -->
                            <div class="cart-item-edit border-top border-white border-opacity-10 bg-black bg-opacity-20">
                                <form action="{{ route('cart.update') }}" method="POST" class="cart-edit-form m-0 p-4">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $row->id }}">
                                    <input type="hidden" name="quantity" class="cart-qty-input" value="{{ $row->quantity }}">

                                    <div class="row g-3">
                                        <!-- Multiplier Adjuster Component Block -->
                                        <div class="col-sm-4">
                                            <label class="form-label text-white-50 small fw-bold tracking-wider text-uppercase mb-2">{{ __('cart.quantity') }}</label>
                                            <div class="cart-qty-control d-flex align-items-center justify-content-between p-1 bg-glass-panel border border-white border-opacity-10 rounded-3">
                                                <button type="button" class="btn btn-sm btn-icon-glass text-white border-0 cart-qty-minus py-1.5"><i class="bi bi-dash-lg"></i></button>
                                                <span class="cart-qty-value text-white fw-bold font-monospace fs-5">{{ $row->quantity }}</span>
                                                <button type="button" class="btn btn-sm btn-icon-glass text-white border-0 cart-qty-plus py-1.5"><i class="bi bi-plus-lg"></i></button>
                                            </div>
                                        </div>

                                        <!-- Modification Instruction Workspace -->
                                        <div class="col-sm-8">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label text-white-50 small fw-bold tracking-wider text-uppercase m-0">{{ __('cart.special_request') }}</label>
                                                <small class="text-white-30 font-monospace fs-xs cart-notes-count">{{ __('cart.notes_counter', ['count' => 0]) }}</small>
                                            </div>
                                            <input type="text" name="notes" class="form-control glass-input cart-notes-input py-2.5 rounded-3"
                                                   maxlength="50" placeholder="{{ __('cart.notes_placeholder') }}"
                                                   value="{{ $row->notes ?? '' }}">
                                        </div>
                                        
                                        <!-- Interface Trigger Options Row Panel -->
                                        <div class="col-12 mt-4 pt-2 border-top border-white border-opacity-5">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-outline-glass px-4 py-2 rounded-pill font-semibold text-white-50 cart-cancel-btn">{{ __('common.cancel') }}</button>
                                                <button type="submit" class="btn btn-primary-orange px-4 py-2 rounded-pill font-semibold shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ __('cart.save_changes') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-glass-card>
        </div>

        <!-- Order Ledger Calculation Column -->
        <div class="col-lg-4 position-sticky" style="top: 1.5rem;">
            <x-glass-card class="p-4 shadow-lg border border-white border-opacity-10 rounded-4">
                <h4 class="h6 text-white-50 fw-bold tracking-wider text-uppercase border-bottom border-white border-opacity-10 pb-3 mb-3">{{ __('cart.summary') }}</h4>
                
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <span class="text-white-50 small">{{ __('common.subtotal') }}</span>
                    <strong class="text-white font-monospace">${{ number_format($subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2.5">
                    <span class="text-white-50 small">{{ __('common.tax') }}</span>
                    <span class="text-white-50 font-monospace">${{ number_format($tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 small">{{ __('common.delivery') }}</span>
                    <span class="text-white-50 font-monospace">${{ number_format($deliveryFee, 2) }}</span>
                </div>
                
                <!-- Logistics Dynamic Alert Banner Badge -->
                <div class="p-2.5 bg-glass-panel rounded-3 border border-white border-opacity-5 mb-4 text-center">
                    @if($deliveryFee > 0)
                        <p class="small text-white-50 m-0 fs-xs">
                            <i class="bi bi-info-circle text-primary-orange me-1"></i>
                            {{ __('cart.delivery_fee_note', ['amount' => number_format($settings->free_delivery_min, 2)]) }}
                        </p>
                    @else
                        <p class="small text-success m-0 fs-xs fw-semibold">
                            <i class="bi bi-lightning-charge-fill me-1"></i>{{ __('cart.free_delivery') }}
                        </p>
                    @endif
                </div>

                <hr class="my-3" style="border-color: rgba(255,255,255,0.15);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="text-white fw-bold">{{ __('common.total') }}</span>
                    <strong class="price-tag text-primary-orange fs-4 font-monospace fw-black">${{ number_format($total, 2) }}</strong>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary-orange py-2.5 rounded-pill fw-bold text-uppercase tracking-wider shadow">
                        {{ __('cart.checkout') }}<i class="bi bi-chevron-right ms-2 fs-xs"></i>
                    </a>
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-glass py-2.5 rounded-pill fw-semibold text-white-50">
                        {{ __('cart.continue_shopping') }}
                    </a>
                </div>
            </x-glass-card>
        </div>
    </div>
    @endif
</div>

@if(!empty($items))
@push('scripts')
<script>
(function() {
    const min = 1, max = 20;
    const notesCounterTemplate = @json(__('cart.notes_counter', ['count' => ':count']));

    function updateNotesCount(input) {
        const counter = input.closest('.cart-item-edit').querySelector('.cart-notes-count');
        if (counter) counter.textContent = notesCounterTemplate.replace(':count', input.value.length);
    }

    document.querySelectorAll('.cart-notes-input').forEach(function(input) {
        updateNotesCount(input);
        input.addEventListener('input', function() { updateNotesCount(input); });
    });

    document.querySelectorAll('.cart-item-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const item = this.closest('.cart-item');
            const wasEditing = item.classList.contains('is-editing');

            document.querySelectorAll('.cart-item').forEach(function(el) {
                el.classList.remove('is-editing');
            });

            if (!wasEditing) {
                item.classList.add('is-editing');
                setTimeout(() => {
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 150); // Small buffer to wait for drawer CSS transition initialization
            }
        });
    });

    document.querySelectorAll('.cart-remove-form').forEach(function(form) {
        form.addEventListener('click', function(e) {
            e.stopPropagation(); // Block row edit accordion expansion from firing when clicking trash bin
        });
    });

    document.querySelectorAll('.cart-cancel-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.closest('.cart-item').classList.remove('is-editing');
        });
    });

    document.querySelectorAll('.cart-item-edit').forEach(function(panel) {
        panel.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        const form = panel.querySelector('.cart-edit-form');
        const qtyInput = form.querySelector('.cart-qty-input');
        const qtyDisplay = form.querySelector('.cart-qty-value');
        let qty = parseInt(qtyInput.value, 10);

        form.querySelector('.cart-qty-minus').addEventListener('click', function() {
            if (qty > min) {
                qty--;
                qtyInput.value = qty;
                qtyDisplay.textContent = qty;
            }
        });

        form.querySelector('.cart-qty-plus').addEventListener('click', function() {
            if (qty < max) {
                qty++;
                qtyInput.value = qty;
                qtyDisplay.textContent = qty;
            }
        });
    });
})();
</script>
@endpush
@endif
@endsection