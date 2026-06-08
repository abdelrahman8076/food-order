<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin Control Panel</title>
    
    <!-- Typography & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --admin-orange: #ff7e67;
            --admin-orange-rgb: 255, 126, 103;
            --admin-yellow: #ffbe76;
            --admin-bg: #0b0f19;
            --admin-card: #151c2c;
            --admin-card-hover: #1c263b;
            --admin-border: rgba(255, 255, 255, 0.06);
            --admin-text-muted: #8492a6;
            --transition-smooth: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Sora', sans-serif;
            background: var(--admin-bg);
            color: #f3f4f6;
            letter-spacing: -0.1px;
            -webkit-font-smoothing: antialiased;
        }

        /* --- Premium Navbar Layout --- */
        .navbar-admin {
            background: rgba(21, 28, 44, 0.8) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--admin-border);
            padding: 0.75rem 1rem;
            sticky-top: 0;
            z-index: 1030;
        }

        .navbar-admin .navbar-brand {
            font-weight: 800;
            color: #fff !important;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-admin .navbar-brand span {
            color: var(--admin-orange);
        }

        .navbar-admin .nav-link {
            color: var(--admin-text-muted) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-admin .nav-link:hover,
        .navbar-admin .nav-item.active .nav-link {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.04);
        }

        .navbar-admin .nav-item.active .nav-link {
            color: var(--admin-orange) !important;
            background: rgba(var(--admin-orange-rgb), 0.08);
        }

        /* --- Global Form Elements Re-engineering --- */
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.04) !important;
            border: 1px solid var(--admin-border) !important;
            color: #fff !important;
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.07) !important;
            border-color: var(--admin-orange) !important;
            box-shadow: 0 0 0 4px rgba(var(--admin-orange-rgb), 0.15) !important;
        }

        /* Auto-adjust native internal dark UI elements for dropdown picks */
        .form-select option, .form-control option, select option {
            background-color: var(--admin-card);
            color: #fff;
        }

        /* --- Component Architecture System --- */
        .card, .admin-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border) !important;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .card-header {
            background: rgba(255, 255, 255, 0.02) !important;
            border-bottom: 1px solid var(--admin-border) !important;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
        }

        /* --- Kanban Board Modules --- */
        .admin-board-column {
            background: rgba(21, 28, 44, 0.4);
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 1.25rem;
            min-height: 500px;
        }

        .admin-board-title {
            color: var(--admin-yellow);
            font-weight: 700;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-order-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.85rem;
            cursor: grab;
            transition: var(--transition-smooth);
        }

        .admin-order-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 6px 15px rgba(0,0,0,0.25);
        }

        /* --- Modern Data Tables Layout --- */
        .table-responsive {
            border-radius: 12px;
            border: 1px solid var(--admin-border);
        }

        .table {
            margin-bottom: 0;
            --bs-table-bg: var(--admin-card);
            --bs-table-color: #f3f4f6;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.03);
            --bs-table-border-color: var(--admin-border);
        }

        .table th {
            background: rgba(255, 255, 255, 0.02);
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--admin-text-muted);
            padding: 1rem 1.25rem;
        }

        .table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
        }

        /* --- Premium UI Utility Components --- */
        .btn-admin-primary {
            background: var(--admin-orange);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            transition: var(--transition-smooth);
        }

        .btn-admin-primary:hover {
            background: #ff654a;
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 126, 103, 0.3);
        }

        .btn-admin-outline {
            background: transparent;
            border: 1px solid var(--admin-border);
            color: #fff;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            transition: var(--transition-smooth);
        }

        .btn-admin-outline:hover {
            border-color: var(--admin-orange);
            color: var(--admin-orange);
            background: rgba(var(--admin-orange-rgb), 0.04);
        }

        .admin-badge {
            background: rgba(var(--admin-orange-rgb), 0.12);
            color: var(--admin-orange);
            font-weight: 600;
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        /* --- Shared utilities --- */
        .fs-xs { font-size: 0.725rem !important; }
        .uppercase { text-transform: uppercase; }
        .tracking-wider { letter-spacing: 0.5px; }
        .max-w-xs { max-width: 320px; }
        .text-accent-yellow { color: var(--admin-yellow) !important; }
        .style-filter-bg { background: rgba(255, 255, 255, 0.01) !important; }
        .rgba-header-tint { background: rgba(255, 255, 255, 0.01) !important; border-bottom: 1px solid var(--admin-border) !important; }
        .truncate-container { max-width: 280px; }
        .hover-orange:hover,
        .text-hover-orange:hover { color: var(--admin-orange) !important; }

        /* --- Icon action buttons --- */
        .btn.btn-icon-action {
            --bs-btn-bg: rgba(255, 255, 255, 0.06);
            --bs-btn-border-color: rgba(255, 255, 255, 0.12);
            --bs-btn-color: rgba(255, 255, 255, 0.8);
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: var(--transition-smooth);
        }

        .btn.btn-icon-action.btn-edit-tint {
            color: var(--admin-orange) !important;
            border-color: rgba(var(--admin-orange-rgb), 0.35) !important;
            background: rgba(var(--admin-orange-rgb), 0.1) !important;
        }

        .btn.btn-icon-action.btn-edit-tint:hover {
            background: rgba(var(--admin-orange-rgb), 0.18) !important;
            border-color: var(--admin-orange) !important;
            color: #fff !important;
        }

        .btn.btn-icon-action.btn-view-tint {
            color: var(--admin-yellow) !important;
            border-color: rgba(255, 190, 118, 0.35) !important;
            background: rgba(255, 190, 118, 0.1) !important;
        }

        .btn.btn-icon-action.btn-view-tint:hover {
            background: rgba(255, 190, 118, 0.18) !important;
            border-color: var(--admin-yellow) !important;
            color: #fff !important;
        }

        .btn.btn-icon-action.btn-delete-tint {
            color: #ff6b81 !important;
            border-color: rgba(255, 107, 129, 0.35) !important;
            background: rgba(255, 107, 129, 0.1) !important;
        }

        .btn.btn-icon-action.btn-delete-tint:hover {
            background: rgba(255, 107, 129, 0.2) !important;
            border-color: #ff6b81 !important;
            color: #fff !important;
        }

        /* --- Unified status pills --- */
        .admin-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            border: 1px solid transparent;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }

        .admin-status-pill--success {
            color: #2ed573;
            background: rgba(46, 213, 115, 0.12);
            border-color: rgba(46, 213, 115, 0.3);
        }

        .admin-status-pill--warning {
            color: #ffbe76;
            background: rgba(255, 190, 118, 0.12);
            border-color: rgba(255, 190, 118, 0.3);
        }

        .admin-status-pill--danger {
            color: #ff6b81;
            background: rgba(255, 107, 129, 0.12);
            border-color: rgba(255, 107, 129, 0.3);
        }

        .admin-status-pill--info {
            color: #6eb5ff;
            background: rgba(110, 181, 255, 0.12);
            border-color: rgba(110, 181, 255, 0.3);
        }

        .admin-status-pill--muted {
            color: rgba(255, 255, 255, 0.55);
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .admin-status-pill--featured {
            color: #ffbe76;
            background: rgba(255, 190, 118, 0.12);
            border-color: rgba(255, 190, 118, 0.3);
        }

        .admin-status-pill__dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: admin-pill-pulse 2s infinite ease-in-out;
        }

        @keyframes admin-pill-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.15); }
        }

        .live-dot-pulse {
            width: 7px;
            height: 7px;
            background-color: #2ed573;
            border-radius: 50%;
            display: inline-block;
            animation: live-indicator-glow 1.8s infinite ease-in-out;
        }

        @keyframes live-indicator-glow {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(46, 213, 115, 0.4); }
            50% { opacity: 0.5; transform: scale(1.1); box-shadow: 0 0 0 4px rgba(46, 213, 115, 0); }
        }

        .avatar-thumbnail-frame {
            width: 44px;
            height: 44px;
        }

        /* --- Interactive Status Badge Notification Elements --- */
        #pendingOrdersBadge {
            background: var(--admin-orange) !important;
            color: #fff !important;
            font-size: 0.7rem;
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(255, 126, 103, 0.5); }
            70% { box-shadow: 0 0 0 6px rgba(255, 126, 103, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 126, 103, 0); }
        }

        /* --- Breadcrumbs --- */
        .breadcrumb-item a {
            color: var(--admin-yellow);
            text-decoration: none;
            font-weight: 500;
        }
        .breadcrumb-item a:hover { text-decoration: underline; }
        .breadcrumb-item.active { color: var(--admin-text-muted); }
        .text-muted { color: var(--admin-text-muted) !important; }

        /* --- System Alerts Styling Refinements --- */
        .system-toast-alert {
            border: none;
            border-left: 4px solid transparent;
            background: var(--admin-card);
            color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .system-toast-alert.alert-success { border-left-color: #2ed573; }
        .system-toast-alert.alert-danger { border-left-color: #ff4757; }

        /* --- Dark theme contrast fixes (Bootstrap overrides) --- */
        .card, .admin-card {
            --bs-card-color: #f3f4f6;
            color: #f3f4f6;
        }

        .card-body .fw-bold,
        .card-body .fs-3,
        .card-body .fs-4,
        .card-body .display-6,
        .card-title {
            color: #f3f4f6;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #fff;
        }

        .table-light,
        thead.table-light th {
            background: rgba(255, 255, 255, 0.05) !important;
            color: var(--admin-text-muted) !important;
            border-color: var(--admin-border) !important;
        }

        .badge.bg-white,
        .badge.bg-white.bg-opacity-5 {
            background: rgba(255, 255, 255, 0.1) !important;
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .badge.bg-warning {
            color: #1a1a1a !important;
        }

        .badge.bg-secondary,
        .badge.bg-success {
            color: #fff !important;
        }

        .btn-outline-primary {
            color: var(--admin-orange);
            border-color: var(--admin-orange);
        }

        .btn-outline-primary:hover {
            background: rgba(var(--admin-orange-rgb), 0.12);
            color: #fff;
            border-color: var(--admin-orange);
        }

        .btn-primary {
            background: var(--admin-orange);
            border-color: var(--admin-orange);
            color: #fff;
        }

        .btn-primary:hover {
            background: #ff654a;
            border-color: #ff654a;
            color: #fff;
        }

        .btn-outline-secondary {
            color: rgba(255, 255, 255, 0.8);
            border-color: var(--admin-border);
        }

        .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .text-white-70 {
            color: rgba(255, 255, 255, 0.7) !important;
        }

        .text-accent-orange {
            color: var(--admin-orange) !important;
        }

        .btn-outline-danger {
            color: #ff6b81;
            border-color: rgba(255, 107, 129, 0.45);
        }

        .btn-outline-danger:hover {
            background: rgba(255, 107, 129, 0.15);
            border-color: #ff6b81;
            color: #fff;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.12);
            color: #fff;
            border-color: rgba(255, 193, 7, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Main Navigation Interface Bar Layout -->
    <nav class="navbar navbar-expand-lg navbar-admin sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill text-slice text-primary-orange me-1"></i>
                EATSHUB <span>PRO</span>
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <i class="bi bi-list fs-3"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                    <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.analytics.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.analytics.index') }}"><i class="bi bi-graph-up-arrow"></i>Analytics</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.orders.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="bi bi-bag-check"></i>Orders
                            <span id="pendingOrdersBadge" class="badge rounded-pill ms-1 d-none">0</span>
                        </a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.items.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.items.index') }}"><i class="bi bi-egg-fried"></i>Items</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated"></i>Coupons</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.categories.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags"></i>Categories</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.delivery-locations.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.delivery-locations.index') }}"><i class="bi bi-map"></i>Locations</a>
                    </li>
                    <li class="nav-item {{ Request::routeIs('admin.settings') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.settings') }}"><i class="bi bi-sliders"></i>Settings</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav d-flex align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link text-white-50" href="{{ route('home') }}" target="_blank">
                            <i class="bi bi-arrow-up-right-square me-1"></i>Live Site
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2 border-start border-secondary border-opacity-25 ps-lg-3">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link border-0 text-danger-hover">
                                <i class="bi bi-box-arrow-right me-1 text-danger"></i>Logout
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
                    <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert system-toast-alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="alert" aria-label="Close"></button>
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