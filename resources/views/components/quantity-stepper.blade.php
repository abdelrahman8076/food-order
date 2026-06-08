@props(['itemId', 'quantity', 'action' => route('cart.update')])

<form action="{{ $action }}" method="POST" class="d-flex align-items-center gap-2">
    @csrf
    <input type="hidden" name="item_id" value="{{ $itemId }}">
    <input type="number" name="quantity" value="{{ $quantity }}" min="1" max="20"
           class="form-control form-control-sm glass-input" style="width: 60px;"
           onchange="this.form.submit()">
</form>
