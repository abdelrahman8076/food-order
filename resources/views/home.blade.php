@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center py-5">
        <h1 class="display-3 fw-800 page-heading">Order <span>Now</span></h1>
        <p class="text-white-50">Homemade food, delivered fresh to your door.</p>
    </div>

    @if($featured->isNotEmpty())
    <div class="mb-5">
        <h2 class="h4 fw-bold mb-4" style="color: var(--accent-yellow);">Today's Specials</h2>
        <div class="row g-3 g-md-4">
            @foreach($featured as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <x-food-card :item="$item" />
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <h2 class="h4 fw-bold mb-4" style="color: var(--accent-yellow);">Browse Categories</h2>
    <div class="row g-4 justify-content-center">
        @forelse($categories as $category)
        <div class="col-6 col-md-5 col-lg-4">
            <a href="{{ route('menu.category', $category->slug) }}" class="portrait-card">
                <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=800&auto=format&fit=crop' }}"
                     class="card-bg" alt="{{ $category->name }}">
                <div class="full-glass-overlay">
                    <h2 class="category-name">{{ $category->name }}</h2>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center text-white-50">
            <p>No categories yet. <a href="{{ route('menu.index') }}" class="text-warning">View menu</a></p>
        </div>
        @endforelse
    </div>
</div>
@endsection
