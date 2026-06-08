@extends('layouts.admin')

@section('title', __('admin.items.edit_title_page'))

@section('content')
<!-- Header & Navigation Context -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('common.admin') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.items.index') }}">{{ __('admin.items.breadcrumb_items') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ __('admin.items.edit_breadcrumb') }}</li>
            </ol>
        </nav>
        <h1 class="h3 m-0 fw-bold text-white">{{ __('admin.items.edit_title_page') }}</h1>
    </div>
    <a href="{{ route('admin.items.index') }}" class="btn btn-admin-outline btn-sm">
        <i class="bi bi-arrow-left me-1"></i> {{ __('admin.items.back_to_list') }}
    </a>
</div>

<!-- Main Form Structural Layout Grid -->
<form action="{{ route('admin.items.update', $item) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row g-4">
        <!-- Left Structural Column: Primary Item Attributes -->
        <div class="col-xl-8 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-muted"></i>
                    <span>{{ __('admin.items.item_details') }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        <!-- Item Name Input Block -->
                        <div class="col-100">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.name_en') }}</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $item->name) }}" placeholder="{{ __('admin.items.name_placeholder') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-100">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $item->name_ar) }}" dir="rtl">
                            @error('name_ar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Category Selector Module -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.category') }}</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ old('category_id', $item->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Pricing Configuration Module -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.base_price') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-0 text-white-50" style="background: rgba(255,255,255,0.02); border-right: 1px solid var(--admin-border) !important;">$</span>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $item->price) }}" step="0.01" min="0" placeholder="0.00" required>
                                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Description Interactive Text Box Block -->
                        <div class="col-100">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.description_en') }}</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="{{ __('admin.items.description_placeholder') }}">{{ old('description', $item->description) }}</textarea>
                        </div>
                        <div class="col-100">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.description_ar') }}</label>
                            <textarea name="description_ar" class="form-control" rows="4" dir="rtl">{{ old('description_ar', $item->description_ar) }}</textarea>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Structural Column: Media Settings & Publishing State Controls -->
        <div class="col-xl-4 col-lg-5">
            <div class="d-flex flex-column gap-4">
                
                <!-- Card Component Area A: Asset Media Uploader Box -->
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-image text-muted"></i>
                        <span>{{ __('admin.items.item_media') }}</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        @if($item->image)
                            <div class="position-relative mb-3 mx-auto overflow-hidden d-inline-block" style="border-radius: 12px; border: 1px solid var(--admin-border); max-width: 100%;">
                                <img src="{{ asset('storage/' . $item->image) }}" alt="Current item view asset" class="img-fluid d-block" style="max-height: 160px; object-fit: cover;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center opacity-0 hover-overlay style-hint" style="background: rgba(0,0,0,0.6); transition: all 0.2s ease;">
                                    <span class="badge bg-dark border border-secondary text-white">{{ __('admin.items.current_asset') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="mb-3 py-4 text-center border border-dashed rounded-3 border-opacity-20 border-white" style="background: rgba(255,255,255,0.01);">
                                <i class="bi bi-cloud-arrow-up text-muted display-6"></i>
                                <p class="small text-muted mt-2 mb-0">{{ __('admin.items.no_image') }}</p>
                            </div>
                        @endif
                        
                        <div class="text-start">
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.upload_image') }}</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text text-muted small mt-1" style="font-size: 0.75rem;">{{ __('admin.items.image_formats') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Card Component Area B: Visibility & Layout Configuration Settings -->
                <div class="card">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-muted"></i>
                        <span>{{ __('admin.items.visibility_sorting') }}</span>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- Toggle Controls Modules Layout Grid -->
                        <div class="d-flex flex-column gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                                <label class="form-check-label fw-medium text-white ms-0" for="is_available">
                                    {{ __('admin.items.available_for_order') }}
                                    <span class="d-block text-muted" style="font-size: 0.75rem; font-weight: 400;">{{ __('admin.items.available_hint') }}</span>
                                </label>
                                <input type="checkbox" name="is_available" class="form-check-input ms-0 float-none" role="switch" id="is_available" value="1" {{ old('is_available', $item->is_available) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                            </div>

                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                                <label class="form-check-label fw-medium text-white ms-0" for="is_featured">
                                    {{ __('admin.items.promote_featured') }}
                                    <span class="d-block text-muted" style="font-size: 0.75rem; font-weight: 400;">{{ __('admin.items.featured_hint') }}</span>
                                </label>
                                <input type="checkbox" name="is_featured" class="form-check-input ms-0 float-none" role="switch" id="is_featured" value="1" {{ old('is_featured', $item->is_featured) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                            </div>
                        </div>

                        <!-- Manual Display Ordering Sorting Inputs Block -->
                        <div>
                            <label class="form-label fw-semibold small text-white-50">{{ __('admin.items.sort_index') }}</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text border-0 text-white-50" style="background: rgba(255,255,255,0.02); border-right: 1px solid var(--admin-border) !important;"><i class="bi bi-sort-numeric-down"></i></span>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $item->sort_order) }}" min="0" placeholder="0">
                            </div>
                            <div class="form-text text-muted small mt-1" style="font-size: 0.75rem;">{{ __('admin.items.sort_hint') }}</div>
                        </div>

                    </div>
                </div>
                
                <!-- Primary Form Operational Layout Submission Actions Row Block -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-admin-primary py-2.5">
                        <i class="bi bi-cloud-check-fill me-1"></i> {{ __('admin.items.save') }}
                    </button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-admin-outline py-2.5">
                        {{ __('admin.items.discard') }}
                    </a>
                </div>

            </div>
        </div>
    </div>
</form>

<!-- Overlay Interactive Style Hint Tweaks -->
@endsection
