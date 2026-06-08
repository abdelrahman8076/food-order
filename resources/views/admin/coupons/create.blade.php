@extends('layouts.admin')

@section('title', __('admin.coupons.add_title'))

@section('content')
<h1 class="mb-4">{{ __('admin.coupons.add_title') }}</h1>
<div class="admin-card card">
    <div class="card-body">
        @include('admin.coupons._form', ['coupon' => null])
    </div>
</div>
@endsection
