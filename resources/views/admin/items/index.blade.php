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
                                <span class="glowing-state-dot-badge d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded text-success bg-success bg-opacity-10 border border-success border-opacity-20 fs-xs fw-semibold uppercase tracking-wider">
                                    <span class="dot-indicator bg-success animate-pulse-soft"></span> Active
                                </span>
                            @else
                                <span class="glowing-state-dot-badge d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded text-muted bg-white bg-opacity-5 border border-white border-opacity-10 fs-xs fw-semibold uppercase tracking-wider">
                                    <span class="dot-indicator bg-secondary"></span> Muted
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($item->is_featured)
                                <span class="glowing-state-dot-badge d-inline-flex align-items-center gap-1.5 py-1 px-2.5 rounded text-warning bg-warning bg-opacity-10 border border-warning border-opacity-20 fs-xs fw-semibold uppercase tracking-wider">
                                    <i class="bi bi-star-fill text-warning fs-xs"></i> Pinned
                                </span>
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

@push('styles')
<style>
    /* Utility Constants Definitions Helper Rules mapping */
    .rgba-header-tint { background: rgba(255,255,255,0.01) !important; border-bottom: 1px solid var(--admin-border) !important; }
    .fs-xs { font-size: 0.725rem !important; }
    .gap-1\.5 { gap: 0.4rem !important; }
    .uppercase { text-transform: uppercase; }
    .tracking-wider { letter-spacing: 0.5px; }
    .max-w-xs { max-width: 320px; }
    .text-white-70 { color: rgba(255,255,255,0.7) !important; }

    /* Core Media Avatar Thumbnail Framework settings formatting rules */
    .avatar-thumbnail-frame {
        width: 44px;
        height: 44px;
    }

    /* Status Pill Base Grid Components Formatting layout */
    .glowing-state-dot-badge {
        font-size: 0.7rem;
    }
    .dot-indicator {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .animate-pulse-soft {
        animation: active-pulse-glowing 2s infinite ease-in-out;
    }
    @keyframes active-pulse-glowing {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.15); }
    }

    /* Isolated Control Icon Buttons Architecture rules styling mapping */
    .btn-icon-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        color: var(--admin-text-muted);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-edit-tint:hover {
        background: rgba(var(--admin-orange-rgb), 0.12);
        border-color: var(--admin-orange);
        color: var(--admin-orange);
    }
    .btn-delete-tint:hover {
        background: rgba(220, 53, 69, 0.15);
        border-color: #dc3545;
        color: #e34c56;
    }
    .text-hover-orange:hover { color: var(--admin-orange) !important; }
    .truncate-container { max-width: 280px; }
</style>
@endpush
@endsection