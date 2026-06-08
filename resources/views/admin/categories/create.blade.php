@extends('layouts.admin')

@section('title', __('admin.categories.add'))

@section('content')
<h1 class="mb-4">{{ __('admin.categories.add') }}</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('admin.categories.name_en') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.categories.name_ar') }}</label>
                <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar') }}" dir="rtl">
                @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.categories.description_en') }}</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.categories.description_ar') }}</label>
                <textarea name="description_ar" class="form-control" rows="2" dir="rtl">{{ old('description_ar') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.categories.sort_order') }}</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">{{ __('common.active') }}</label>
            </div>
            <button type="submit" class="btn btn-admin-primary">{{ __('admin.categories.create') }}</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
        </form>
    </div>
</div>
@endsection
