@extends('layouts.app')

<style>
    :root {
    --accent-yellow: #ffc107;
}

/* Typography & Accent Accents */
.fw-black {
    font-weight: 900;
}
.italic-serif {
    font-family: 'Playfair Display', Georgia, serif;
    font-style: italic;
    font-weight: 400;
}
.tracking-widest {
    letter-spacing: 2px;
}
.editorial-dash {
    display: inline-block;
    width: 24px;
    height: 2px;
    background-color: var(--accent-yellow);
}
.editorial-title {
    font-weight: 800;
    letter-spacing: -0.5px;
    margin-bottom: 6px;
}
.title-subline {
    width: 40px;
    height: 3px;
    background-color: var(--accent-yellow);
}

/* Premium Specials Cards Wrap */
.premium-card-wrapper {
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.premium-card-wrapper:hover {
    transform: translateY(-8px);
}

/* Magazine Category Grid Cards */
.magazine-category-card {
    position: relative;
    display: block;
    height: 260px;
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}
.magazine-img-zoom {
    width: 100%;
    height: 100%;
    transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}
.magazine-category-card:hover .magazine-img-zoom {
    transform: scale(1.06);
}
.magazine-gradient-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.1) 100%);
    transition: opacity 0.4s ease;
}
.magazine-category-card:hover .magazine-gradient-overlay {
    opacity: 0.95;
}
.magazine-card-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1.75rem;
    z-index: 2;
}
.explore-btn-text {
    opacity: 0;
    transform: translateY(5px);
    transition: all 0.3s ease;
}
.magazine-category-card:hover .explore-btn-text {
    opacity: 1;
    transform: translateY(0);
    color: #fff !important;
}

/* Subtle Link Interaction */
.hover-link {
    transition: color 0.2s ease, letter-spacing 0.2s ease;
}
.hover-link:hover {
    color: #fff !important;
    letter-spacing: 3px;
}

/* Dashed border for empty states */
.border-dashed {
    border-style: dashed !important;
}
</style>
@section('content')
<div class="container py-5">
    
    <!-- Editorial Split Hero Section -->
    <div class="row align-items-center mb-5 pb-4 border-bottom border-secondary border-opacity-25">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="editorial-dash"></span>
                <span class="text-uppercase tracking-widest small text-warning fw-bold">Premium Homemade Kitchen</span>
            </div>
            <h1 class="display-3 fw-black page-heading text-white mb-3">
                Crave It. <br class="d-none d-md-block">Order <span class="text-accent-yellow italic-serif">Now.</span>
            </h1>
        </div>
        <div class="col-lg-5">
            <p class="lead text-white-50 border-start border-warning border-3 ps-3 py-2 my-0 lh-base">
                Skip the industrial kitchen. Enjoy thoughtfully curated, fresh homemade dishes delivered directly from our stove to your table.
            </p>
        </div>
    </div>

    <!-- Today's Specials (Clean, Flat Masonry Vibe) -->
    @if($featured->isNotEmpty())
    <div class="mb-5 pb-4">
        <div class="mb-4">
            <h2 class="editorial-title text-white">Today’s Curated Specials</h2>
            <div class="title-subline"></div>
        </div>
        <div class="row g-4">
            @foreach($featured as $item)
            <div class="col-md-6 col-lg-3">
                <div class="premium-card-wrapper">
                    <x-food-card :item="$item" />
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Category Section (Magazine Style Blocks) -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="editorial-title text-white">Explore Our Collections</h2>
                <div class="title-subline"></div>
            </div>
            <a href="{{ route('menu.index') }}" class="text-warning text-uppercase small tracking-widest text-decoration-none fw-bold hover-link">
                Full Menu ↗
            </a>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
            <!-- Dynamically changes width pattern for a premium, non-uniform layout grid -->
            <div class="{{ $loop->iteration % 3 == 0 ? 'col-12' : 'col-md-6' }}">
                <a href="{{ route('menu.category', $category->slug) }}" class="magazine-category-card">
                    <div class="magazine-img-zoom">
                        <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1200&auto=format&fit=crop' }}"
                             alt="{{ $category->name }}" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="magazine-gradient-overlay"></div>
                    <div class="magazine-card-content">
                        <span class="text-uppercase tracking-widest text-warning small mb-1 d-block fw-semibold">Collection</span>
                        <h3 class="h2 text-white m-0 fw-bold">{{ $category->name }}</h3>
                        <span class="explore-btn-text text-white-50 small mt-2 d-inline-block">View Recipes →</span>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center text-white-50 py-5 border border-secondary border-dashed rounded-3">
                <p class="mb-0">No categories curated yet. <a href="{{ route('menu.index') }}" class="text-warning">Browse full kitchen</a></p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection