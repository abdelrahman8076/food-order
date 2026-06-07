@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="container">
    <h1 class="mb-4">Your Cart</h1>

    @if(empty($items))
        <div class="alert alert-info">
            Your cart is empty. <a href="{{ route('menu.index') }}">Browse menu</a> to add items.
        </div>
        <a href="{{ route('menu.index') }}" class="btn btn-primary">View Menu</a>
    @else

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @foreach($items as $row)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <div>
                                <strong>{{ $row->name }}</strong>
                                <span class="text-muted ms-2">${{ number_format($row->price, 2) }} each</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $row->id }}">
                                    <input type="number" name="quantity" value="{{ $row->quantity }}" min="1" max="20" class="form-control form-control-sm" style="width: 60px;" onchange="this.form.submit()">
                                </form>
                                <span class="fw-bold">${{ number_format($row->total, 2) }}</span>
                                <form action="{{ route('cart.remove', $row->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Summary</h5>
                    <p class="mb-0">Subtotal: <strong>${{ number_format($subtotal, 2) }}</strong></p>
                    <p class="small text-muted">Tax & delivery calculated at checkout.</p>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Proceed to Checkout</a>
                    <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary w-100 mt-2">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
