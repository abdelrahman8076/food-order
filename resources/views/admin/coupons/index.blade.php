@extends('layouts.admin')

@section('title', __('admin.coupons.title'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ __('admin.coupons.title') }}</h1>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-admin-primary">{{ __('admin.coupons.add') }}</a>
</div>

<div class="admin-card card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 admin-table">
            <thead>
                <tr>
                    <th>{{ __('admin.coupons.col_code') }}</th>
                    <th>{{ __('admin.coupons.col_type') }}</th>
                    <th>{{ __('admin.coupons.col_value') }}</th>
                    <th>{{ __('admin.coupons.col_min_order') }}</th>
                    <th>{{ __('admin.coupons.col_uses') }}</th>
                    <th>{{ __('admin.coupons.col_expires') }}</th>
                    <th>{{ __('admin.coupons.col_active') }}</th>
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
                                <x-admin.status-pill variant="success" :dot="true">{{ __('common.active') }}</x-admin.status-pill>
                            @else
                                <x-admin.status-pill variant="muted">{{ __('common.inactive') }}</x-admin.status-pill>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-admin-outline">{{ __('common.edit') }}</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm(@json(__('admin.coupons.delete_confirm', ['code' => $coupon->code])));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('common.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-muted p-4">{{ __('admin.coupons.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
