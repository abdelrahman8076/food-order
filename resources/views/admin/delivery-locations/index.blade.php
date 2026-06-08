@extends('layouts.admin')

@section('title', 'Delivery Locations')

@section('content')
<!-- Page Identity Header Block -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Logistics</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt text-accent-orange fs-4"></i>
            <h1 class="h3 m-0 fw-bold text-white">Delivery Locations</h1>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Command & Configuration Modifiers -->
    <div class="col-lg-4">
        
        <!-- Module A: Add City Card -->
        <div class="admin-card card mb-4 border-0">
            <div class="card-header py-3 d-flex align-items-center gap-2 border-bottom style-header-border bg-white bg-opacity-1">
                <i class="bi bi-building text-accent-orange"></i>
                <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">Add Regional City</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.delivery-locations.cities.store') }}" method="POST" class="m-0">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium uppercase tracking-wider mb-2">City Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Cairo" required>
                    </div>
                    <button type="submit" class="btn btn-admin-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-plus-circle"></i> Register City
                    </button>
                </form>
            </div>
        </div>

        <!-- Module B: Add Area Sub-region Card -->
        <div class="admin-card card border-0">
            <div class="card-header py-3 d-flex align-items-center gap-2 border-bottom style-header-border bg-white bg-opacity-1">
                <i class="bi bi-map text-accent-yellow"></i>
                <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">Add Area Sector</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.delivery-locations.areas.store') }}" method="POST" class="m-0">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium uppercase tracking-wider mb-2">Parent City Assignment</label>
                        <select name="city_id" class="form-select" required>
                            <option value="">Select city container...</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium uppercase tracking-wider mb-2">Area Sector Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Nasr City" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-medium uppercase tracking-wider mb-2">Standard Delivery Fee</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white bg-opacity-5 text-muted border-secondary border-opacity-25">$</span>
                            <input type="number" name="delivery_fee" class="form-control" value="{{ old('delivery_fee', $defaultDeliveryFee) }}" step="0.01" min="0" required>
                        </div>
                        <div class="form-text text-muted fs-xs mt-2">
                            <i class="bi bi-info-circle me-1"></i>Set to <strong class="text-success">$0.00</strong> to toggle permanent free shipping.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-admin-primary w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-pin-map"></i> Register Area Sector
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Database Tree Manifest Grid View -->
    <div class="col-lg-8">
        <div class="admin-card card border-0">
            <div class="card-header py-3 border-bottom style-header-border bg-white bg-opacity-1 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-grid-3x3-gap text-muted"></i>
                    <h5 class="h6 m-0 fw-bold text-white uppercase tracking-wider">Active Logistics Network</h5>
                </div>
                <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 text-white-50 fs-xs py-1.5 px-2.5 rounded">
                    Total Cities: {{ $cities->count() }}
                </span>
            </div>
            
            <div class="card-body p-4">
                @forelse($cities as $city)
                    <div class="city-group-container p-3 rounded-3 border mb-3 transition-all">
                        
                        <!-- City Profile Header Row Component -->
                        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary border-opacity-10">
                            <div class="d-flex align-items-center gap-2.5">
                                <h4 class="h5 mb-0 text-white fw-bold">{{ $city->name }}</h4>
                                <span class="badge bg-admin-orange bg-opacity-10 text-accent-orange border border-admin-orange border-opacity-20 fs-xs px-2 py-1">
                                    {{ $city->deliveryAreas->count() }} {{ Str::plural('sector', $city->deliveryAreas->count()) }}
                                </span>
                            </div>
                            
                            <form action="{{ route('admin.delivery-locations.cities.destroy', $city) }}" method="POST"
                                  onsubmit="return confirm('Completely purge {{ addslashes($city->name) }} logistics node? You must permanently drop all child areas first.');" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 py-1 px-2.5 fs-xs fw-medium rounded-2">
                                    <i class="bi bi-trash3"></i> Drop City
                                </button>
                            </form>
                        </div>

                        <!-- Sub-Regions Child List Element Node Tree -->
                        <div class="pt-2">
                            @if($city->deliveryAreas->isEmpty())
                                <div class="d-flex align-items-center gap-2 text-muted small py-2 px-1 italicized">
                                    <i class="bi bi-patch-exclamation opacity-50"></i> No registered sub-sectors assigned to this city hub.
                                </div>
                            @else
                                <div class="list-group list-group-flush">
                                    @foreach($city->deliveryAreas as $area)
                                        <div class="list-group-item px-1 py-2.5 bg-transparent border-secondary border-opacity-10 text-white">
                                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                                                
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-chevron-right text-muted opacity-25 fs-xs"></i>
                                                    <span class="fw-semibold text-white-85 fs-6">{{ $area->name }}</span>
                                                </div>
                                                
                                                <!-- Action Inline Control Configurations -->
                                                <div class="d-flex align-items-center justify-content-end gap-2 w-100-mobile">
                                                    <form action="{{ route('admin.delivery-locations.areas.update', $area) }}" method="POST" class="d-flex align-items-center gap-2 m-0 bg-dark bg-opacity-40 p-1.5 rounded border border-white border-opacity-5">
                                                        @csrf
                                                        @method('PATCH')
                                                        <span class="small text-muted font-sans uppercase tracking-wider fs-xs px-1">Fee:</span>
                                                        <div class="input-group input-group-sm" style="width: 6.5rem;">
                                                            <span class="input-group-text bg-transparent border-0 text-muted ps-1 pe-0">$</span>
                                                            <input type="number" name="delivery_fee" class="form-control form-control-sm bg-transparent text-white fw-bold border-0 font-monospace p-0"
                                                                   value="{{ number_format($area->delivery_fee, 2, '.', '') }}" step="0.01" min="0" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-sm btn-admin-outline py-1 px-2.5 font-sans fs-xs rounded">
                                                            Save
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('admin.delivery-locations.areas.destroy', $area) }}" method="POST"
                                                          onsubmit="return confirm('Erase {{ addslashes($area->name) }} from fulfillment logs?');" class="m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon-action btn-delete-tint" title="Delete current area sector configuration">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted display-5 mb-2"><i class="bi bi-map-fill opacity-25"></i></div>
                        <h6 class="text-white fw-semibold">No Regional Nodes Logged</h6>
                        <p class="text-muted small max-w-xs mx-auto mb-0">Create baseline cities and configure specific delivery zones to map custom freight rates.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection