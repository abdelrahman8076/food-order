@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container">
    <div class="text-center py-4 py-md-5">
        <h1 class="display-3 fw-800 page-heading mb-3">Our <span>Menu</span></h1>
        <p class="text-white-50 mb-4 px-3">Browse our homemade selection.</p>

        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4 px-3">
            <a href="{{ route('menu.index') }}"
               class="btn btn-sm rounded-pill px-4 {{ request()->routeIs('menu.index') ? 'btn-primary-orange' : 'btn-outline-glass' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('menu.category', $cat->slug) }}"
                   class="btn btn-sm rounded-pill px-4 {{ request()->routeIs('menu.category') && request()->route('slug') === $cat->slug ? 'btn-primary-orange' : 'btn-outline-glass' }}">
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
                <div class="col-6 col-md-4 col-lg-3">
                    <x-food-card :item="$item" />
                </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>
@endsection
