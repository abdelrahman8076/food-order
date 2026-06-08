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
            overflow-x: hidden;
        }

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

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
        }

        .glass-card .card-header {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid var(--glass-border);
            color: #fff;
            font-weight: 600;
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid var(--glass-border) !important;
            color: #fff !important;
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.12) !important;
            border-color: var(--primary-orange) !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 126, 103, 0.25) !important;
            color: #fff !important;
        }

        .glass-input::placeholder { color: rgba(255, 255, 255, 0.4); }

        .glass-input, select.glass-input {
            color-scheme: dark;
        }
        .glass-input option,
        select.glass-input option {
            background-color: #1a1a1a;
            color: #fff;
        }

        .btn-primary-orange {
            background: var(--primary-orange);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            transition: 0.2s;
        }

        .btn-primary-orange:hover {
            background: #ff6b52;
            color: white;
        }

        .btn-outline-glass {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: white;
            border-radius: 12px;
        }

        .btn-outline-glass:hover {
            background: var(--glass);
            color: white;
            border-color: var(--primary-orange);
        }

        .food-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-width: 0;
            max-width: 100%;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .food-header {
            position: relative;
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.2);
        }

        .food-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: transform 0.6s ease;
        }

        .food-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 50%, rgba(10, 10, 10, 0.6) 100%);
            pointer-events: none;
        }

        .food-card .card-body {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
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
            transition: 0.2s;
        }

        .btn-add:hover { background: #ff6b52; color: white; }

        .sold-out-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .portrait-card {
            position: relative;
            aspect-ratio: 3 / 4;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            transition: transform 0.4s ease;
            display: block;
            text-decoration: none;
        }

        .portrait-card:hover {
            transform: scale(1.02);
            border-color: var(--primary-orange);
        }

        .portrait-card .card-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .portrait-card .full-glass-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(2px);
            transition: all 0.4s ease;
        }

        .portrait-card:hover .full-glass-overlay {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(0);
        }

        .category-name {
            color: white;
            margin: 0;
            font-weight: 800;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .breadcrumb-glass {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-glass .breadcrumb-item a {
            color: var(--accent-yellow);
            text-decoration: none;
        }

        .breadcrumb-glass .breadcrumb-item.active { color: rgba(255, 255, 255, 0.6); }

        .page-heading {
            font-weight: 800;
        }

        .page-heading span { color: var(--primary-orange); }

        .sticky-checkout-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(15px);
            border-top: 1px solid var(--glass-border);
            padding: 1rem;
            z-index: 1000;
            display: none;
        }

        @media (max-width: 768px) {
            .sticky-checkout-bar { display: block; }
            main { padding-bottom: 100px !important; }
        }

        .row.g-4 > [class*="col-"] { min-width: 0; }

        @media (max-width: 576px) {
            .display-3 { font-size: 2.2rem; }
            .food-card { border-radius: 16px; }
            .food-title { font-size: 0.9rem; }
            .category-name { font-size: 1.1rem; letter-spacing: 2px; }
        }

        @media (min-width: 992px) {
            .food-card:hover {
                transform: translateY(-8px);
                border-color: var(--primary-orange);
            }
            .food-card:hover img { transform: scale(1.05); }
            .navbar { background: transparent; }
            .navbar.scrolled { background: rgba(10, 10, 10, 0.95); }
        }

        main { padding: 20px 0 60px; }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">EATS<span style="color:white; -webkit-text-fill-color: white;">HUB</span></a>

            <div class="d-flex align-items-center gap-2 order-lg-last">
                <a class="nav-link cart-pill" href="{{ route('cart.index') }}" id="cartPill">
                    <i class="bi bi-bag"></i>
                    <span id="cartCount">{{ \App\Support\CartHelper::cartCount(session('cart', [])) ?: '0' }}</span>
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
                    @auth
                        <li class="nav-item"><a class="nav-link text-white px-3" href="{{ route('my-orders') }}">My Orders</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <x-flash-messages />
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.onscroll = function() {
            let nav = document.getElementById('mainNav');
            if (window.scrollY > 20) nav.classList.add('scrolled');
            else nav.classList.remove('scrolled');
        };

        window.addToCart = function(itemId, btn) {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            fetch('/cart/add/' + itemId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(r => r.json())
            .then(data => {
                const countEl = document.getElementById('cartCount');
                if (countEl) countEl.textContent = data.cart_count;
                if (btn) {
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="bi bi-check-lg"></i>';
                    btn.style.background = '#28a745';
                    setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; }, 1200);
                }
            });
        };
    </script>
    @stack('scripts')
</body>
</html>
