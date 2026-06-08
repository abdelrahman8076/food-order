@extends('layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<h1 class="mb-4">Edit Coupon</h1>
<div class="admin-card card">
    <div class="card-body">
        @include('admin.coupons._form', ['coupon' => $coupon])
    </div>
</div>
@endsection
