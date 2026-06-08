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
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>

<body class="layout-public">
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
