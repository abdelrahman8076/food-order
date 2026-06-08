<!DOCTYPE html>
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.dashboard.title')) - {{ __('admin.nav.title_suffix') }}</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if($isRtl)
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="layout-admin">

    <!-- Main Navigation Interface Bar Layout -->
    <nav class="navbar navbar-expand-lg navbar-admin sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill text-slice text-primary-orange me-1"></i>
                {{ __('admin.nav.brand') }} <span>{{ __('admin.nav.brand_pro') }}</span>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>{{ __('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.analytics.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.analytics.index') }}"><i class="bi bi-graph-up-arrow"></i>{{ __('admin.nav.analytics') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-bag-check"></i>{{ __('admin.nav.orders') }}
                            <span id="pendingOrdersBadge" class="badge rounded-pill ms-1 d-none">0</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.items.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.items.index') }}"><i class="bi bi-egg-fried"></i>{{ __('admin.nav.items') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated"></i>{{ __('admin.nav.coupons') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i>{{ __('admin.nav.categories') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.delivery-locations.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.delivery-locations.index') }}"><i class="bi bi-map"></i>{{ __('admin.nav.locations') }}</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.settings') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.settings') }}"><i class="bi bi-sliders"></i>{{ __('admin.nav.settings') }}</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav d-flex align-items-center gap-2">
                    <li class="nav-item">
                        <x-locale-switcher />
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-arrow-up-right-square me-1"></i>{{ __('admin.nav.live_site') }}
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 border-start border-secondary border-opacity-25 ps-lg-3">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link border-0 text-danger-hover">
                                <i class="bi bi-box-arrow-right me-1 text-danger"></i>{{ __('admin.nav.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Global Layout System Flash Messages Notification Center -->
    @if(session('success') || session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080; margin-top: 4.5rem;">
            @if(session('success'))
                <div class="alert system-toast-alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="alert" aria-label="{{ __('common.close') }}"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert system-toast-alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="alert" aria-label="{{ __('common.close') }}"></button>
                </div>
            @endif
        </div>
    @endif

    <!-- Content Scaffolding Injection Slot -->
    <div class="container-fluid py-4 px-md-4">
        @yield('content')
    </div>

    <!-- Core Runtime Bootstrap Bundles Layout Engine Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('admin.partials._new-order-notifications')
    @stack('scripts')
</body>
</html>
