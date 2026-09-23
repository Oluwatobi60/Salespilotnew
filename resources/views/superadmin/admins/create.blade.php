@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'New Superadmin')

@section('superadmin_layout_content')

<div class="mb-4">
    <a href="{{ route('superadmin.admins') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Administrators
    </a>
    <h5 class="fw-bold mt-2 mb-1">New Superadmin</h5>
    <p class="text-muted small mb-0">Register a new system administrator</p>
</div>

<div class="sa-card" style="max-width:600px;">
    <form method="POST" action="{{ route('superadmin.admins.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-medium small">Full Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium small">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium small">Phone Number (Optional)</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium small">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Minimum 8 characters</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium small">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('superadmin.admins') }}" class="btn btn-light border">Cancel</a>
            <button type="submit" class="btn btn-primary px-4">Register Admin</button>
        </div>
    </form>
</div>

@endsection
