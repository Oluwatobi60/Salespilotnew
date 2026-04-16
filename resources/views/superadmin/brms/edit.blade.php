@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Edit BRM')

@section('superadmin_layout_content')

<div class="mb-4">
    <a href="{{ route('superadmin.brms') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to BRM list
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sa-card">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Edit BRM — {{ $brm->name }}</h5>
                <p class="text-muted small mb-0">Update Business Relation Manager details</p>
            </div>

            <form method="POST" action="{{ route('superadmin.brms.update', $brm->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $brm->name) }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $brm->email) }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $brm->phone) }}"
                               class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Region / Territory</label>
                        <input type="text" name="region" value="{{ old('region', $brm->region) }}"
                               class="form-control @error('region') is-invalid @enderror">
                        @error('region')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" value="{{ old('address', $brm->address) }}"
                               class="form-control @error('address') is-invalid @enderror">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Referral Code</label>
                        <input type="text" name="referral_code" value="{{ old('referral_code', $brm->referral_code) }}"
                               class="form-control" readonly title="Unique code assigned to this BRM">
                        <small class="text-muted d-block mt-1">Auto-generated: {{ $brm->referral_code ?? 'Not assigned' }}</small>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $brm->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Leave blank to keep current">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Confirm new password">
                    </div>

                    <div class="col-12 d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('superadmin.brms') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
