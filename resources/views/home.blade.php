@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center" style="padding: 92px;">
        <h1 class="display-3 fw-800">Order <span style="color: var(--primary-orange);">Now</span></h1>
        <!-- <p class="text-white-50">Premium food categories.</p> -->
    </div>

    <div class="row g-4 justify-content-center">
        @php
            $categories = [
                [
                    'name' => 'Pasta', 
                    'image' => 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?q=80&w=800&auto=format&fit=crop'
                ],
                [
                    'name' => 'Dessert', 
                    'image' => 'https://images.unsplash.com/photo-1551024506-0bccd828d307?q=80&w=800&auto=format&fit=crop'
                ],
            ];
        @endphp

        @foreach($categories as $item)
        <div class="col-6 col-md-5 col-lg-4">
            <div class="portrait-card">
                <img src="{{ $item['image'] }}" class="card-bg" alt="{{ $item['name'] }}">
                
                <div class="full-glass-overlay">
                    <h2 class="category-name">{{ $item['name'] }}</h2>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Premium Eats</title>

    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
        --primary-orange: #ff7e67;
        --glass-white: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.15);
    }

    body {
        background-color: #0a0a0a;
        font-family: 'Sora', sans-serif;
    }

    /* Portrait Card ✅ */
    .portrait-card {
        position: relative;
        aspect-ratio: 3 / 4; 
        border-radius: 32px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        transition: transform 0.4s ease;
    }

    /* Image Behind ✅ */
    .card-bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    /* Full Image Glass Overlay ✅ */
    .full-glass-overlay {
        position: absolute;
        inset: 0; /* Cover everything */
        z-index: 2;
        display: flex;
        justify-content: center;
        align-items: center;
        
        /* The "Glass" effect over the whole image */
        background: rgba(0, 0, 0, 0.2); /* Slight tint so text pops */
        backdrop-filter: blur(4px); /* Subtle blur across the whole image */
        -webkit-backdrop-filter: blur(4px);
        
        transition: all 0.4s ease;
    }

    /* Text Only ✅ */
    .category-name {
        color: white;
        margin: 0;
        font-weight: 800;
        font-size: 2rem;
        text-transform: uppercase;
        letter-spacing: 20px;
        z-index: 3;
        /* Extra glass box for the text itself */
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0px;
    }

    /* Hover State: Reveal image more clearly */
    .portrait-card:hover .full-glass-overlay {
        backdrop-filter: blur(0px); /* Clear the glass on hover */
        background: rgba(0, 0, 0, 0.1);
    }

    .portrait-card:hover {
        transform: scale(1.02);
    }

    @media (max-width: 576px) {
        .category-name { font-size: 1.2rem; padding: 0.8rem 1.5rem; }
    }

        /* --- NAVBAR STYLES --- */
        .navbar {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: white !important;
        }
        .cart-pill {
            background: var(--primary-orange);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            color: white !important;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* --- THE FLEXIBLE GLASS CARD --- */
        .food-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden; /* KEY: This clips the image to the border radius */
            display: flex;
            flex-direction: column;
            height: 100%; /* Makes all cards in a row the same height */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* --- THE IMAGE HEADER --- */
        .food-header {
            position: relative;
            width: 100%;
            /* FLEXIBILITY: Aspect Ratio keeps image proportional (16:9 widescreen) */
            aspect-ratio: 16/9;
            overflow: hidden;
        }

        .food-header img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensures image fills the box without stretching */
            transition: transform 0.6s ease;
        }

        /* Gradient Overlay to blend image into card */
        .food-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(20, 20, 20, 0.8) 100%);
        }

        /* --- CARD CONTENT --- */
        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            z-index: 2;
        }

        .food-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .food-desc {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            line-height: 1.4;
            flex-grow: 1; /* Pushes the price section to the bottom */
        }

        .price-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-tag {
            color: var(--accent-yellow);
            font-weight: 800;
            font-size: 1.1rem;
        }

        .btn-add {
            background: white;
            color: black;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .btn-add:hover {
            background: var(--primary-orange);
            color: white;
        }

        /* HOVER EFFECTS */
        .food-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .food-card:hover img {
            transform: scale(1.08);
        }

        /* Mobile Tweak */
        @media (max-width: 576px) {
            .food-header { aspect-ratio: 4/3; } /* Taller images on mobile */
        }
    </style>
</head>

