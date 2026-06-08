@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <x-glass-card title="Login">
                @error('email')
                    <div class="alert alert-danger border-0 mb-3" style="background: rgba(220,53,69,0.2); color: #fff;">{{ $message }}</div>
                @enderror
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-white-50">Email</label>
                        <input type="email" name="email" class="form-control glass-input" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Password</label>
                        <input type="password" name="password" class="form-control glass-input" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label text-white-50" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary-orange w-100">Login</button>
                </form>
            </x-glass-card>
        </div>
    </div>
</div>
@endsection
