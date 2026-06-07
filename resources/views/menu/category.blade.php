@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>
    <h1 class="mb-4">{{ $category->name }}</h1>
    @if($category->description)
        <p class="text-muted">{{ $category->description }}</p>
    @endif

    <div class="row g-3">
        @forelse($category->activeItems as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $item->name }}</h6>
                        @if($item->description)
                            <p class="card-text small text-muted flex-grow-1">{{ $item->description }}</p>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fw-bold text-primary">${{ number_format($item->price, 2) }}</span>
                            <form action="{{ route('cart.add', $item) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">No items in this category right now.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">View full menu</a>
    </div>
</div>
@endsection
