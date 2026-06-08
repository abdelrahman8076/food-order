@extends('layouts.admin')

@section('title', __('admin.categories.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ __('admin.categories.title') }}</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-admin-primary">{{ __('admin.categories.add') }}</a>
</div>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('admin.categories.col_name') }}</th>
                    <th>{{ __('admin.categories.col_slug') }}</th>
                    <th>{{ __('admin.categories.col_items') }}</th>
                    <th>{{ __('admin.categories.col_order') }}</th>
                    <th>{{ __('admin.categories.col_active') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->slug }}</td>
                        <td>{{ $cat->items_count }}</td>
                        <td>{{ $cat->sort_order }}</td>
                        <td>
                            @if($cat->is_active)
                                <x-admin.status-pill variant="success" :dot="true">{{ __('common.active') }}</x-admin.status-pill>
                            @else
                                <x-admin.status-pill variant="muted">{{ __('common.inactive') }}</x-admin.status-pill>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-admin-outline">{{ __('common.edit') }}</a>
                            <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm(@json(__('admin.categories.delete_confirm')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
