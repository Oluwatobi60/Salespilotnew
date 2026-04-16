@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Register BRM')

@section('superadmin_layout_content')
{{--  <link rel="stylesheet" href="{{ asset('manager_asset/css/password-validation.css') }}">  --}}

<div class="mb-4">
    <a href="{{ route('superadmin.brms') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to BRM list
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="sa-card">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Register New BRM</h5>
                <p class="text-muted small mb-0">Add a Business Relation Manager to the platform</p>
            </div>

            <form id="createBrmForm" method="POST" action="{{ route('superadmin.brms.store') }}">
                @csrf

                <div class="row g-3">
                    <!-- Name -->
                    <div class="col-6">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="e.g. John Adeyemi">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="john@example.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+234 800 000 0000">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Region -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Region / Territory</label>
                        <input type="text" name="region" value="{{ old('region') }}"
                               class="form-control @error('region') is-invalid @enderror"
                               placeholder="e.g. Lagos, South West">
                        @error('region')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>




                    <!-- Password -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                     <!-- Address -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="form-control @error('address') is-invalid @enderror"
                               placeholder="Street, City">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Referral code -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Referral Code <span class="text-muted">(Auto-generated)</span></label>
                        <div class="input-group">
                            <input type="text" id="referral_code_input" name="referral_code" value="{{ old('referral_code') }}"
                                   class="form-control @error('referral_code') is-invalid @enderror"
                                   placeholder="Click Generate to create" readonly>
                            <button type="button" class="btn btn-outline-secondary" id="generateCodeBtn" title="Generate new code">
                                <i class="bi bi-arrow-repeat"></i> Generate
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Unique 6-character alphanumeric code for this BRM</small>
                        @error('referral_code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="Any additional information about this BRM…">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="col-12 d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-person-plus me-1"></i> Register BRM
                        </button>
                        <a href="{{ route('superadmin.brms') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('manager_asset/js/password-validator.js') }}"></script>
<script src="{{ asset('manager_asset/js/password-validation.js') }}"></script>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateCodeBtn');
    const referralCodeInput = document.getElementById('referral_code_input');

    generateBtn.addEventListener('click', function() {
        generateReferralCode();
    });

    // Auto-generate code on page load if field is empty
    if (!referralCodeInput.value) {
        generateReferralCode();
    }

    function generateReferralCode() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';

        for (let i = 0; i < 6; i++) {
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        referralCodeInput.value = code;
        referralCodeInput.classList.remove('is-invalid');
    }
});
</script>
