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

            <form method="POST" action="{{ route('superadmin.brms.update', $brm->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <!-- Profile Photo -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div id="photoPreviewWrapper" style="width:80px;height:80px;border-radius:50%;overflow:hidden;border:2px solid #dee2e6;background:#f8f9fa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                @if($brm->profile_photo)
                                    <img id="photoPreview" src="{{ asset('brm_photos/'.$brm->profile_photo) }}" alt="{{ $brm->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    <i id="photoPlaceholderIcon" class="bi bi-person-fill" style="font-size:2rem;color:#adb5bd;display:none;"></i>
                                @else
                                    <img id="photoPreview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <i id="photoPlaceholderIcon" class="bi bi-person-fill" style="font-size:2rem;color:#adb5bd;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" id="profile_photo_input" name="profile_photo"
                                       class="form-control @error('profile_photo') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <small class="text-muted d-block mt-1">JPG, PNG or GIF — max 2 MB. Leave blank to keep current photo.</small>
                                @error('profile_photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput   = document.getElementById('profile_photo_input');
    const photoPreview = document.getElementById('photoPreview');
    const photoIcon    = document.getElementById('photoPlaceholderIcon');
    const wrapper      = document.getElementById('photoPreviewWrapper');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    photoIcon.style.display = 'none';
                    wrapper.style.border = '2px solid #4299e1';
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>


@endsection
