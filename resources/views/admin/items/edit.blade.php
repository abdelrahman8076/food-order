@extends('layouts.admin')

@section('title', 'Edit Item')

@section('content')
<h1 class="mb-4">Edit Menu Item</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Category *</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $item->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $item->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price *</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $item->price) }}" step="0.01" min="0" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                @if($item->image)
                    <div class="mb-2"><img src="{{ asset('storage/' . $item->image) }}" alt="" style="max-height:80px;border-radius:8px;"></div>
                @endif
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label">Sort order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order) }}" min="0">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_available" class="form-check-input" id="is_available" value="1" {{ old('is_available', $item->is_available) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_available">Available</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1" {{ old('is_featured', $item->is_featured) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">Featured</label>
            </div>
            <button type="submit" class="btn btn-primary">Update Item</button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
