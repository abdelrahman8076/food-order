@extends('layouts.admin')

@section('title', __('admin.items.add_title'))

@section('content')
<h1 class="mb-4">{{ __('admin.items.add_title') }}</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.category') }}</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.name_en') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.name_ar') }}</label>
                <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" dir="rtl">
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.description_en') }}</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.description_ar') }}</label>
                <textarea name="description_ar" class="form-control" rows="2" dir="rtl">{{ old('description_ar') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.price') }}</label>
                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.items.sort_order') }}</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_available" class="form-check-input" id="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_available">{{ __('admin.items.available') }}</label>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">{{ __('admin.items.featured') }}</label>
            </div>
            <button type="submit" class="btn btn-admin-primary">{{ __('admin.items.create') }}</button>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
        </form>
    </div>
</div>
@endsection
