@extends('layouts.app')

@section('title', __('menu.title'))

@section('content')
<div class="container py-4">
    <!-- Header Hero -->
    <div class="text-center py-4 py-md-5 menu-hero-section">
        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.75rem;">
            {{ __('menu.badge') }}
        </span>
        <h1 class="display-3 fw-black page-heading mb-3 text-white">
            {{ __('menu.heading_our') }} <span class="italic-serif text-accent-yellow">{{ __('menu.heading_menu') }}</span>
        </h1>
        <p class="text-white-50 mb-0 mx-auto style-subtext" style="max-width: 500px;">
            {{ __('menu.subtext') }}
        </p>
    </div>
</div>

<!-- Premium Smooth-Scroll Category Tracker -->
<div class="menu-filter-sticky sticky-top py-2 backdrop-blur">
    <div class="container">
        <div class="d-flex flex-nowrap justify-content-start justify-content-md-center gap-2 overflow-auto hide-scrollbar mask-gradient">
            <a href="{{ route('menu.index') }}"
               class="btn btn-menu-pill menu-filter-pill px-4 flex-shrink-0"
               data-filter="all">
               {{ __('menu.filter_all') }}
            </a>
            @foreach($categories as $cat)
                <a href="#cat-{{ $cat->slug }}"
                   class="btn btn-menu-pill menu-filter-pill px-4 flex-shrink-0"
                   data-filter="{{ $cat->slug }}">
                   {{ $cat->localizedName() }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="container py-4 pt-0">
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
                                    <span class="text-accent-yellow italic-serif me-1">#</span>{{ $category->localizedName() }}
                                </h2>
                            </div>
                            <span class="text-white-50 small tracking-wider fw-semibold text-uppercase">
                                {{ __('menu.choices', ['count' => $category->activeItems->count()]) }}
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

@push('scripts')
<script>
(function() {
    const pills = document.querySelectorAll('.menu-filter-pill');
    const sections = document.querySelectorAll('.scroll-margin-target[id^="cat-"]');

    function setActiveFilter(slug) {
        pills.forEach(function(pill) {
            pill.classList.toggle('active', pill.dataset.filter === slug);
        });
    }

    function slugFromHash() {
        const hash = location.hash.replace(/^#cat-/, '');
        return hash || 'all';
    }

    function syncFromHash() {
        setActiveFilter(slugFromHash());
    }

    pills.forEach(function(pill) {
        pill.addEventListener('click', function() {
            setActiveFilter(pill.dataset.filter);
        });
    });

    window.addEventListener('hashchange', syncFromHash);
    syncFromHash();

    if (sections.length && 'IntersectionObserver' in window) {
        let scrollObserver = null;

        function stickyOffset() {
            const nav = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--public-navbar-height'), 10) || 64;
            const filter = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--menu-filter-height'), 10) || 56;
            return nav + filter + 8;
        }

        function bindScrollSpy() {
            if (scrollObserver) scrollObserver.disconnect();
            scrollObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        setActiveFilter(entry.target.id.replace(/^cat-/, ''));
                    }
                });
            }, {
                root: null,
                rootMargin: '-' + stickyOffset() + 'px 0px -55% 0px',
                threshold: 0,
            });
            sections.forEach(function(section) {
                scrollObserver.observe(section);
            });
        }

        bindScrollSpy();
        window.addEventListener('resize', bindScrollSpy);

        window.addEventListener('scroll', function() {
            if (window.scrollY < 80 && !location.hash) {
                setActiveFilter('all');
            }
        }, { passive: true });
    }
})();
</script>
@endpush
