@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Edit Plan')

@section('superadmin_layout_content')

<div class="mb-4">
    <a href="{{ route('superadmin.plans') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Plans
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="sa-card">
            <div class="mb-4">
                <h5 class="fw-bold mb-1">Edit Plan — <span class="text-capitalize">{{ $plan->name }}</span></h5>
                <p class="text-muted small mb-0">Update the details for this subscription plan</p>
            </div>

            <form method="POST" action="{{ route('superadmin.plans.update', $plan->id) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <!-- Plan Name -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}"
                               class="form-control @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Monthly Price -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Monthly Price (&#8358;) <span class="text-danger">*</span></label>
                        <input type="number" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}"
                               step="0.01" min="0"
                               class="form-control @error('monthly_price') is-invalid @enderror">
                        @error('monthly_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $plan->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Features -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Features</label>
                        <textarea name="features" rows="6"
                                  class="form-control @error('features') is-invalid @enderror"
                                  placeholder="One feature per line…">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                        <div class="form-text">Enter one feature per line.</div>
                        @error('features')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Limits -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Max Managers <span class="text-danger">*</span></label>
                        <input type="number" name="max_managers" value="{{ old('max_managers', $plan->max_managers) }}"
                               min="1"
                               class="form-control @error('max_managers') is-invalid @enderror">
                        @error('max_managers')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Max Staff</label>
                        <input type="number" name="max_staff" value="{{ old('max_staff', $plan->max_staff) }}"
                               min="1"
                               class="form-control @error('max_staff') is-invalid @enderror"
                               placeholder="Leave blank = unlimited">
                        @error('max_staff')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Max Branches</label>
                        <input type="number" name="max_branches" value="{{ old('max_branches', $plan->max_branches) }}"
                               min="1"
                               class="form-control @error('max_branches') is-invalid @enderror"
                               placeholder="Leave blank = unlimited">
                        @error('max_branches')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Trial Days -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Trial Days <span class="text-danger">*</span></label>
                        <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}"
                               min="0"
                               class="form-control @error('trial_days') is-invalid @enderror">
                        @error('trial_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Toggles -->
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   name="is_popular" id="is_popular" value="1"
                                   {{ old('is_popular', $plan->is_popular) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_popular">Mark as Popular</label>
                        </div>
                    </div>

                    <!-- Subscriber info (read-only) -->
                    <div class="col-12">
                        <div class="alert alert-light border d-flex align-items-center gap-2 mb-0" style="font-size:0.85rem;">
                            <i class="bi bi-info-circle text-primary"></i>
                            This plan currently has <strong>{{ $plan->userSubscriptions()->count() }}</strong> total subscription(s).
                            Editing the plan does not affect existing active subscriptions.
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12 d-flex gap-2 pt-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                        <a href="{{ route('superadmin.plans') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

@endsection
