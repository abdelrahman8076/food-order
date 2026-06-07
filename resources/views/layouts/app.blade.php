<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Premium Eats') - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-orange: #ff7e67;
            --accent-yellow: #ffbe76;
            --glass: rgba(255, 255, 255, 0.07);
            --glass-border: rgba(255, 255, 255, 0.12);
            --bg-overlay: rgba(10, 10, 10, 0.88);
        }

        body {
            font-family: 'Sora', sans-serif;
            color: #ffffff;
            background: linear-gradient(var(--bg-overlay), var(--bg-overlay)),
                url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1974&auto=format&fit=crop');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* --- RESPONSIVE NAVBAR --- */
        .navbar {
            padding: 1rem 0;
            transition: 0.3s;
            background: rgba(10, 10, 10, 0.5);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            background: linear-gradient(45deg, var(--primary-orange), var(--accent-yellow));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cart-pill {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            color: white !important;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* --- MOBILE OPTIMIZED CARDS --- */
        .food-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .food-title {
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
        }

        .food-desc {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 0.75rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-tag {
            color: var(--accent-yellow);
            font-weight: 800;
            font-size: 1rem;
        }

        .btn-add {
            background: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 12px;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* --- MOBILE BREAKPOINTS --- */
        @media (max-width: 576px) {
            .display-3 { font-size: 2.2rem; }
            .food-card { border-radius: 16px; }
            .food-title { font-size: 0.9rem; }
            .container { padding-left: 15px; padding-right: 15px; }
            .row.g-3 { --bs-gutter-x: 0.75rem; --bs-gutter-y: 0.75rem; }
        }

        @media (min-width: 992px) {
            .food-card:hover {
                transform: translateY(-8px);
                border-color: var(--primary-orange);
            }
            .navbar { background: transparent; }
            .navbar.scrolled { background: rgba(10, 10, 10, 0.95); }
        }

        main { padding: 20px 0 60px; }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">EATS<span style="color:white; -webkit-text-fill-color: white;">HUB</span></a>
            
            <div class="d-flex align-items-center gap-2 order-lg-last">
                <a class="nav-link cart-pill" href="{{ route('cart.index') }}">
                    <i class="bi bi-bag"></i>
                    <span>{{ array_sum(session('cart', [])) ?: '0' }}</span>
                </a>
                <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-list text-white fs-2"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-center py-3 py-lg-0">
                    <li class="nav-item"><a class="nav-link text-white px-3" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="{{ route('menu.index') }}">Menu</a></li>
                    <li class="nav-item"><a class="nav-link text-white px-3" href="{{ route('order.track') }}">Track</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onscroll = function() {
            let nav = document.getElementById('mainNav');
            if (window.scrollY > 20) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        };
    </script>
</body>
</html>