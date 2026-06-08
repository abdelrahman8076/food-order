@extends('layouts.app')
@section('content')
<div class="container py-5">
    
    <!-- Editorial Split Hero Section -->
    <div class="row align-items-center mb-5 pb-4 border-bottom border-secondary border-opacity-25">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="editorial-dash"></span>
                <span class="text-uppercase tracking-widest small text-warning fw-bold">{{ __('home.badge') }}</span>
            </div>
            <h1 class="display-3 fw-black page-heading text-white mb-3">
                {{ __('home.heading_line1') }} <br class="d-none d-md-block">{{ __('home.heading_line2') }} <span class="text-accent-yellow italic-serif">{{ __('home.heading_accent') }}</span>
            </h1>
        </div>
        <div class="col-lg-5">
            <p class="lead text-white-50 border-start border-warning border-3 ps-3 py-2 my-0 lh-base">
                {{ __('home.lead') }}
            </p>
        </div>
    </div>

    <!-- Today's Specials (Clean, Flat Masonry Vibe) -->
    @if($featured->isNotEmpty())
    <div class="mb-5 pb-4">
        <div class="mb-4">
            <h2 class="editorial-title text-white">{{ __('home.specials_title') }}</h2>
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
                <h2 class="editorial-title text-white">{{ __('home.collections_title') }}</h2>
                <div class="title-subline"></div>
            </div>
            <a href="{{ route('menu.index') }}" class="text-warning text-uppercase small tracking-widest text-decoration-none fw-bold hover-link">
                {{ __('home.full_menu_link') }}
            </a>
        </div>

        <div class="row g-4">
            @forelse($categories as $category)
            <!-- Dynamically changes width pattern for a premium, non-uniform layout grid -->
            <div class="{{ $loop->iteration % 3 == 0 ? 'col-12' : 'col-md-6' }}">
                <a href="{{ route('menu.category', $category->slug) }}" class="magazine-category-card">
                    <div class="magazine-img-zoom">
                        <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=1200&auto=format&fit=crop' }}"
                             alt="{{ $category->localizedName() }}" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="magazine-gradient-overlay"></div>
                    <div class="magazine-card-content">
                        <span class="text-uppercase tracking-widest text-warning small mb-1 d-block fw-semibold">{{ __('home.collection_label') }}</span>
                        <h3 class="h2 text-white m-0 fw-bold">{{ $category->localizedName() }}</h3>
                        <span class="explore-btn-text text-white-50 small mt-2 d-inline-block">{{ __('home.view_recipes') }}</span>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center text-white-50 py-5 border border-secondary border-dashed rounded-3">
                <p class="mb-0">{{ __('home.no_categories') }} <a href="{{ route('menu.index') }}" class="text-warning">{{ __('home.browse_kitchen') }}</a></p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
