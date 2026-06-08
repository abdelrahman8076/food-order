<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --admin-orange: #ff7e67;
            --admin-yellow: #ffbe76;
            --admin-bg: #121212;
            --admin-card: #1e1e1e;
            --admin-border: rgba(255,255,255,0.1);
        }
        body {
            font-family: 'Sora', sans-serif;
            background: var(--admin-bg);
            color: #fff;
        }
        .navbar-admin {
            background: #0a0a0a;
            border-bottom: 1px solid var(--admin-border);
        }
        .navbar-admin .navbar-brand {
            font-weight: 800;
            color: var(--admin-orange) !important;
        }
        .navbar-admin .nav-link { color: rgba(255,255,255,0.7) !important; }
        .navbar-admin .nav-link:hover { color: var(--admin-orange) !important; }
        .navbar-admin .nav-item form { margin: 0; display: flex; align-items: center; }
        .navbar-admin .btn-link.nav-link {
            color: rgba(255,255,255,0.7) !important;
            text-decoration: none;
            padding: 0.5rem 1rem;
            line-height: var(--bs-nav-link-line-height);
        }
        .navbar-admin .btn-link.nav-link:hover { color: var(--admin-orange) !important; }
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #fff;
            --bs-table-hover-bg: rgba(255,255,255,0.06);
            --bs-table-hover-color: #fff;
            --bs-table-border-color: var(--admin-border);
        }
        .table-light {
            --bs-table-bg: rgba(255,255,255,0.05);
            --bs-table-color: #fff;
        }
        .card {
            background: var(--admin-card);
            border-color: var(--admin-border);
            color: #fff;
        }
        .card .card-header, .card .card-footer {
            background: rgba(255,255,255,0.05);
            border-color: var(--admin-border);
            color: #fff;
        }
        .pagination {
            --bs-pagination-bg: var(--admin-card);
            --bs-pagination-border-color: var(--admin-border);
            --bs-pagination-color: rgba(255,255,255,0.8);
            --bs-pagination-hover-bg: rgba(255,255,255,0.1);
            --bs-pagination-hover-color: var(--admin-orange);
            --bs-pagination-active-bg: var(--admin-orange);
            --bs-pagination-active-border-color: var(--admin-orange);
            --bs-pagination-disabled-color: rgba(255,255,255,0.35);
        }
        .text-muted { color: rgba(255,255,255,0.5) !important; }
        .breadcrumb { --bs-breadcrumb-divider-color: rgba(255,255,255,0.4); }
        .form-control::placeholder { color: rgba(255,255,255,0.45); }
        .admin-card, .card.admin-card {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            color: #fff;
        }
        .admin-card .card-header {
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid var(--admin-border);
            color: #fff;
        }
        .admin-board-column {
            background: var(--admin-card);
            border: 1px solid var(--admin-border);
            border-radius: 12px;
            padding: 1rem;
            min-height: 200px;
        }
        .admin-board-title {
            color: var(--admin-yellow);
            font-weight: 700;
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }
        .admin-order-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .btn-admin-primary {
            background: var(--admin-orange);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-admin-primary:hover { background: #ff6b52; color: white; }
        .btn-admin-outline {
            background: transparent;
            border: 1px solid var(--admin-border);
            color: #fff;
        }
        .btn-admin-outline:hover { border-color: var(--admin-orange); color: var(--admin-orange); }
        .admin-badge { background: rgba(255,126,103,0.2); color: var(--admin-orange); }
        .admin-table { color: #fff; }
        .admin-table thead { background: rgba(255,255,255,0.05); }
        .admin-table td, .admin-table th { border-color: var(--admin-border); color: #fff; }
        .form-control, .form-select {
            background: rgba(255,255,255,0.08);
            border-color: var(--admin-border);
            color: #fff;
        }
        .form-control, .form-select, select {
            color-scheme: dark;
        }
        .form-select option,
        .form-control option,
        select option {
            background-color: var(--admin-card);
            color: #fff;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.12);
            border-color: var(--admin-orange);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255,126,103,0.25);
        }
        .breadcrumb-item a { color: var(--admin-yellow); }
        .breadcrumb-item.active { color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-admin">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">EATSHUB Admin</a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            Orders
                            <span id="pendingOrdersBadge" class="badge rounded-pill ms-1 d-none">0</span>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.items.index') }}">Items</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.coupons.index') }}">Coupons</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.delivery-locations.index') }}">Locations</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Settings</a></li>
                </ul>
                <ul class="navbar-nav d-flex align-items-center">
                    <li class="nav-item d-flex align-items-center"><a class="nav-link" href="{{ route('home') }}">View Site</a></li>
                    <li class="nav-item d-flex align-items-center">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link border-0">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 rounded-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="container-fluid py-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('admin.partials._new-order-notifications')
    @stack('scripts')
</body>
</html>
