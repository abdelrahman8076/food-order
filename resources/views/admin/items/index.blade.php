@extends('layouts.admin')

@section('title', 'Items')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Menu Items</li>
            </ol>
        </nav>
        <h1 class="h3 m-0 fw-bold text-white">Menu Catalog</h1>
    </div>
    <a href="{{ route('admin.items.create') }}" class="btn btn-admin-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-circle-fill"></i> Add New Menu Item
    </a>
</div>

<div class="card shadow-sm border-0">
    
    <div class="card-header p-3 rgba-header-tint">
        <form action="{{ url()->current() }}" method="GET" id="catalogFilterForm" class="m-0">
            <div class="row g-2 align-items-center justify-content-between">
                
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25"><i class="bi bi-filter"></i></span>
                        <select name="category_id" class="form-select form-select-sm" onchange="document.getElementById('catalogFilterForm').submit()">
                            <option value="">All Categories Displayed</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-auto">
                    <span class="text-white-50 small fw-medium">
                        Showing <span class="text-accent-yellow fw-bold">{{ $items->count() }}</span> items this page
                    </span>
                </div>

            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 45%;">Item Name Details</th>
                    <th style="width: 15%;">Category</th>
                    <th style="width: 12%;">Base Price</th>
                    <th style="width: 11%;">Availability</th>
                    <th style="width: 11%;">Featured</th>
                    <th style="width: 6%;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-thumbnail-frame flex-shrink-0 bg-dark rounded-3 overflow-hidden border border-white border-opacity-10 d-flex align-items-center justify-content-center">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }} Thumbnail" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <i class="bi bi-egg-fried text-muted opacity-50 fs-5"></i>
                                    @endif
                                </div>
                                <div class="truncate-container">
                                    <h6 class="text-white fw-semibold mb-0 text-truncate text-hover-orange transition-all">{{ $item->name }}</h6>
                                    <span class="text-muted small d-block">ID: #{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-white bg-opacity-5 text-white-70 border border-white border-opacity-10 px-2.5 py-1.5 rounded-pill fs-xs">
                                {{ $item->category->name }}
                            </span>
                        </td>
                        
                        <td>
                            <span class="text-white fw-bold font-serif fs-6">${{ number_format($item->price, 2) }}</span>
                        </td>

                        <td>
                            @if($item->is_available)
                                <x-admin.status-pill variant="success" :dot="true">Active</x-admin.status-pill>
                            @else
                                <x-admin.status-pill variant="muted">Muted</x-admin.status-pill>
                            @endif
                        </td>

                        <td>
                            @if($item->is_featured)
                                <x-admin.status-pill variant="featured">
                                    <i class="bi bi-star-fill fs-xs"></i> Pinned
                                </x-admin.status-pill>
                            @else
                                <span class="text-white-50 opacity-25 px-2">—</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1.5">
                                <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-icon-action btn-edit-tint" title="Edit Item parameters configuration">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('Are you sure you want to completely erase {{ addslashes($item->name) }} from current operational databases? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon-action btn-delete-tint" title="Delete product item forever">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted display-6 mb-2"><i class="bi bi-inbox-fill opacity-20"></i></div>
                            <h6 class="text-white fw-semibold">No Menu Items Found</h6>
                            <p class="text-muted small max-w-xs mx-auto mb-0">No entries matched your specified category. Try clearing filters or create a new inventory asset entry.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<div class="mt-4 custom-pagination-wrapper">
    {{ $items->withQueryString()->links() }}
</div>
@endsection