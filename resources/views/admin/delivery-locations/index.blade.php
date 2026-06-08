@extends('layouts.admin')

@section('title', 'Delivery Locations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Delivery Locations</h1>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="admin-card card">
            <div class="card-header">Add City</div>
            <div class="card-body">
                <form action="{{ route('admin.delivery-locations.cities.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">City name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Cairo" required>
                    </div>
                    <button type="submit" class="btn btn-admin-primary w-100">Add City</button>
                </form>
            </div>
        </div>

        <div class="admin-card card mt-4">
            <div class="card-header">Add Area</div>
            <div class="card-body">
                <form action="{{ route('admin.delivery-locations.areas.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <select name="city_id" class="form-select" required>
                            <option value="">Select city</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Area name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Nasr City" required>
                    </div>
                    <button type="submit" class="btn btn-admin-primary w-100">Add Area</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="admin-card card">
            <div class="card-header">Cities & Areas</div>
            <div class="card-body">
                @forelse($cities as $city)
                    <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--admin-border) !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ $city->name }}</h5>
                            <form action="{{ route('admin.delivery-locations.cities.destroy', $city) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $city->name }}? You must remove all areas first.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete City</button>
                            </form>
                        </div>

                        @if($city->deliveryAreas->isEmpty())
                            <p class="text-muted small mb-0">No areas yet.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($city->deliveryAreas as $area)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0"
                                        style="background: transparent; border-color: var(--admin-border); color: #fff;">
                                        <span>{{ $area->name }}</span>
                                        <form action="{{ route('admin.delivery-locations.areas.destroy', $area) }}" method="POST"
                                              onsubmit="return confirm('Delete {{ $area->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">No cities yet. Add Cairo and your delivery areas to get started.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
