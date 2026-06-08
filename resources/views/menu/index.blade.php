@extends('layouts.app')

@section('title', 'Menu')

@section('content')

<style>

    :root {
    --accent-yellow: #ffc107;
    --primary-orange: #ff7f50; /* Fallback highlight matching your brand styles */
    --glass-border: rgba(255, 255, 255, 0.1);
}

/* Typography Extensions */
.fw-black {
    font-weight: 900;
}
.tracking-wider {
    letter-spacing: 1.5px;
}
.tracking-tight {
    letter-spacing: -0.5px;
}
.italic-serif {
    font-family: 'Playfair Display', Georgia, serif;
    font-style: italic;
    font-weight: 400;
}

/* Luxury Linear Accents */
.custom-editorial-line {
    width: 45px;
    height: 3px;
    background: linear-gradient(90deg, var(--accent-yellow), transparent);
    margin-top: 8px;
    margin-bottom: 24px;
    border-radius: 2px;
}

/* Enhanced Sticky Glass Filter Bar Configuration */
.menu-filter-sticky {
    z-index: 1020;
    margin-top: 1rem;
    border-radius: 50px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}
.backdrop-blur {
    background: rgba(15, 15, 15, 0.75);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
}

/* Custom Modernized Menu Pills */
.btn-menu-pill {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--glass-border);
    color: rgba(255, 255, 255, 0.65);
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    padding: 0.55rem 1.4rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
}
.btn-menu-pill:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.2);
}
.btn-menu-pill.active, 
.btn-menu-pill:focus-within {
    background: linear-gradient(135deg, var(--accent-yellow), #e0a800) !important;
    color: #111 !important;
    border-color: var(--accent-yellow) !important;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.35);
    font-weight: 700;
}

/* Fluid Horizontal Layout Hacks for Mobile Screens */
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Graceful anchor scroll adjustment to avoid running underneath the sticky top navbar */
.scroll-margin-target {
    scroll-margin-top: 90px;
}

/* Smooth Interactive Micro-animations */
.menu-card-transform {
    transition: transform 0.3s cubic-bezier(0.215, 0.610, 0.355, 1);
}
.menu-card-transform:hover {
    transform: translateY(-6px) scale(1.01);
}

/* Horizontal fade mask for overflowing category text on mobile devices */
@media (max-width: 767.98px) {
    .mask-gradient {
        mask-image: linear-gradient(to right, transparent 0%, #000 5%, #000 95%, transparent 100%);
        -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 5%, #000 95%, transparent 100%);
    }
}
</style>
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