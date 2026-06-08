@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Coupons</h1>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin-primary">Add Coupon</a>
</div>

<div class="admin-card card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Uses</th>
                    <th>Expires</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td><code>{{ $coupon->code }}</code></td>
                        <td>{{ ucfirst($coupon->type) }}</td>
                        <td>{{ $coupon->formattedValue() }}</td>
                        <td>{{ $coupon->min_order_amount ? '$' . number_format($coupon->min_order_amount, 2) : '—' }}</td>
                        <td>
                            {{ $coupon->used_count }}
                            @if($coupon->max_uses)
                                / {{ $coupon->max_uses }}
                            @endif
                        </td>
                        <td>{{ $coupon->expires_at ? $coupon->expires_at->format('M d, Y') : '—' }}</td>
                        <td>
                            @if($coupon->is_active)
                                <x-admin.status-pill variant="success" :dot="true">Active</x-admin.status-pill>
                            @else
                                <x-admin.status-pill variant="muted">Inactive</x-admin.status-pill>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-admin-outline">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted p-4">No coupons yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
