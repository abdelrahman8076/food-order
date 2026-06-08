@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('content')
<h1 class="mb-4">Add Coupon</h1>
<div class="admin-card card">
    <div class="card-body">
        @include('admin.coupons._form', ['coupon' => null])
    </div>
</div>
@endsection
