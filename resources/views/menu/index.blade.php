@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container">
    <div class="text-center py-4 py-md-5">
        <h1 class="display-3 fw-800 mb-3">Our <span style="color: var(--primary-orange);">Menu.</span></h1>
        <p class="text-white-50 mb-4 px-3">Browse our premium selection of handcrafted dishes.</p>
        
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4 px-3">
            <a href="{{ route('menu.index') }}" class="btn btn-sm rounded-pill px-4 {{ !request('cat') ? 'btn-primary border-0' : 'btn-outline-light' }}" style="{{ !request('cat') ? 'background: var(--primary-orange);' : '' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('menu.category', $cat->slug) }}" 
                   class="btn btn-sm rounded-pill px-4 {{ request('cat') == $cat->slug ? 'btn-primary border-0' : 'btn-outline-light' }}"
                   style="{{ request('cat') == $cat->slug ? 'background: var(--primary-orange);' : '' }}">
                   {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>

    @foreach($categories as $category)
        @if($category->activeItems->isNotEmpty())
            <div class="row g-3 g-md-4 mb-5">
                <div class="col-12 mt-4">
                    <h2 class="h4 fw-bold mb-0" style="color: var(--accent-yellow);">{{ $category->name }}</h2>
                    <hr class="mt-2 mb-4" style="border-color: var(--glass-border); opacity: 1;">
                </div>

                @foreach($category->activeItems as $item)
                <div class="col-6 col-md-4 col-lg-3" id="item-{{ $item->id }}">
                    <div class="card food-card">
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="food-title text-truncate mb-1">{{ $item->name }}</h6>
                            <p class="food-desc mb-3">
                                {{ $item->description ?: 'No description available for this delicious item.' }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price-tag">${{ number_format($item->price, 2) }}</span>
                                
                                <form action="{{ route('cart.add', $item) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endsection