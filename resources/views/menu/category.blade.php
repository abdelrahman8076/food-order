@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb breadcrumb-glass">
            <li class="breadcrumb-item"><a href="{{ route('menu.index') }}">Menu</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <h1 class="page-heading mb-2">{{ $category->name }}</h1>
    @if($category->description)
        <p class="text-white-50 mb-4">{{ $category->description }}</p>
    @endif

    <div class="row g-3 g-md-4">
        @forelse($category->activeItems as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <x-food-card :item="$item" />
            </div>
        @empty
            <div class="col-12">
                <x-glass-card>
                    <p class="text-white-50 mb-0">No items in this category right now.</p>
                </x-glass-card>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        <a href="{{ route('menu.index') }}" class="btn btn-outline-glass">View full menu</a>
    </div>
</div>
@endsection
