@extends('layouts.admin')

@section('title', 'Items')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Menu Items</h1>
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">Add Item</a>
</div>
<form class="mb-3">
    <div class="row g-2">
        <div class="col-auto">
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</form>
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Available</th>
                    <th>Featured</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td>${{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
                        <td>{{ $item->is_featured ? 'Yes' : 'No' }}</td>
                        <td>
                            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $items->withQueryString()->links() }}</div>
@endsection
