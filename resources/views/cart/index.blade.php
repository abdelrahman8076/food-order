@extends('layouts.app')

@section('title', 'Cart')
@section('content')
<div class="container">
    <h1 class="page-heading mb-4">Your <span>Cart</span></h1>

    @if(empty($items))
        <x-glass-card>
            <p class="text-white-50 mb-3">Your cart is empty.</p>
            <a href="{{ route('menu.index') }}" class="btn btn-primary-orange">Browse Menu</a>
        </x-glass-card>
    @else
    <div class="row g-4">
        <div class="col-lg-8">
            <x-glass-card>
                @foreach($items as $row)
                    <div class="cart-item" id="cart-item-{{ $row->id }}" data-item-id="{{ $row->id }}">
                        <div class="cart-item-row d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <span class="text-white fw-bold">{{ $row->name }}</span>
                                <span class="text-white-50 small ms-1">${{ number_format($row->price, 2) }} each</span>
                                <div class="text-white-50 small mt-1">Qty: {{ $row->quantity }}</div>
                                @if(!empty($row->notes))
                                    <div class="small text-white-50 mt-1"><i class="bi bi-chat-left-text me-1"></i>{{ $row->notes }}</div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                <span class="price-tag">${{ number_format($row->total, 2) }}</span>
                                <form action="{{ route('cart.remove', $row->id) }}" method="POST" class="d-inline cart-remove-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-glass"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="cart-item-edit">
                            <form action="{{ route('cart.update') }}" method="POST" class="cart-edit-form">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $row->id }}">
                                <input type="hidden" name="quantity" class="cart-qty-input" value="{{ $row->quantity }}">

                                <label class="form-label text-white-50 small mb-2">Quantity</label>
                                <div class="cart-qty-control mb-3">
                                    <button type="button" class="cart-qty-btn cart-qty-minus">−</button>
                                    <span class="cart-qty-value">{{ $row->quantity }}</span>
                                    <button type="button" class="cart-qty-btn cart-qty-plus">+</button>
                                </div>

                                <label class="form-label text-white-50 small mb-2">Special request (optional)</label>
                                <input type="text" name="notes" class="form-control glass-input cart-notes-input mb-1"
                                       maxlength="50" placeholder="No onions, extra sauce..."
                                       value="{{ $row->notes ?? '' }}">
                                <div class="d-flex justify-content-end mb-3">
                                    <small class="text-white-50 cart-notes-count">0/50</small>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-orange flex-grow-1">Save changes</button>
                                    <button type="button" class="btn btn-outline-glass cart-cancel-btn">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </x-glass-card>
        </div>
        <div class="col-lg-4">
            <x-glass-card title="Summary">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Subtotal</span>
                    <strong class="text-white">${{ number_format($subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50">Tax</span>
                    <span class="text-white">${{ number_format($tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-white-50">Delivery</span>
                    <span class="text-white">${{ number_format($deliveryFee, 2) }}</span>
                </div>
                @if($deliveryFee > 0)
                    <p class="small text-white-50 mb-2">
                        Delivery fee depends on your area for orders under ${{ number_format($settings->free_delivery_min, 2) }}.
                    </p>
                @else
                    <p class="small text-white-50 mb-2">Free delivery — your order qualifies.</p>
                @endif
                <hr style="border-color: var(--glass-border);">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-white fw-bold">Total</span>
                    <strong class="price-tag fs-5">${{ number_format($total, 2) }}</strong>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-primary-orange w-100">Proceed to Checkout</a>
                <a href="{{ route('menu.index') }}" class="btn btn-outline-glass w-100 mt-2">Continue Shopping</a>
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

    function updateNotesCount(input) {
        const counter = input.closest('.cart-item-edit').querySelector('.cart-notes-count');
        if (counter) counter.textContent = input.value.length + '/50';
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
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });

    document.querySelectorAll('.cart-remove-form').forEach(function(form) {
        form.addEventListener('click', function(e) {
            e.stopPropagation();
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
