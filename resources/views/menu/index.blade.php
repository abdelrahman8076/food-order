@extends('layouts.app')

@section('title', 'Menu')

@section('content')
<div class="container py-4">
    <!-- Header Hero -->
    <div class="text-center py-4 py-md-5 menu-hero-section">
        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.75rem;">
            ✨ Freshly Prepared
        </span>
        <h1 class="display-3 fw-black page-heading mb-3 text-white">
            Our <span class="italic-serif text-accent-yellow">Menu</span>
        </h1>
        <p class="text-white-50 mb-4 mx-auto style-subtext" style="max-width: 500px;">
            Browse our premium selection of homemade dishes cooked right to order.
        </p>

        <!-- Premium Smooth-Scroll Category Tracker -->
        <div class="menu-filter-sticky sticky-top py-2 backdrop-blur">
            <div class="d-flex flex-nowrap justify-content-start justify-content-md-center gap-2 px-3 overflow-auto hide-scrollbar mask-gradient">
                <a href="{{ route('menu.index') }}"
                   class="btn btn-menu-pill px-4 flex-shrink-0 {{ request()->routeIs('menu.index') ? 'active' : '' }}">
                   All Content
                </a>
                @foreach($categories as $cat)
                    <!-- Added #hash fallback target for intuitive single-page scanning UI -->
                    <a href="{{ request()->routeIs('menu.index') ? '#cat-'.$cat->slug : route('menu.category', $cat->slug) }}"
                       class="btn btn-menu-pill px-4 flex-shrink-0 {{ request()->routeIs('menu.category') && request()->route('slug') === $cat->slug ? 'active' : '' }}">
                       {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Active Menu Collections Grid -->
    <div class="menu-sections-wrapper mt-2">
        @foreach($categories as $category)
            @if($category->activeItems->isNotEmpty())
                <!-- Target Anchor ID allows instant view-jumping -->
                <div class="row g-3 g-md-4 mb-5 scroll-margin-target" id="cat-{{ $category->slug }}">
                    <div class="col-12">
                        <div class="d-flex align-items-end justify-content-between mb-2">
                            <div>
                                <h2 class="h4 fw-black m-0 text-white tracking-tight">
                                    <span class="text-accent-yellow italic-serif me-1">#</span>{{ $category->name }}
                                </h2>
                            </div>
                            <span class="text-white-50 small tracking-wider fw-semibold text-uppercase">
                                {{ $category->activeItems->count() }} Choices
                            </span>
                        </div>
                        <div class="custom-editorial-line"></div>
                    </div>

                    @foreach($category->activeItems as $item)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="menu-card-transform h-100">
                            <x-food-card :item="$item" />
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection