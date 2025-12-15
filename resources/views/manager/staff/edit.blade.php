@extends('manager.layouts.layout')
@section('manager_page_title')
Edit Staff
@endsection
@section('manager_layout_content')
  <div class="modal-body">

    <h2> Edit Profile</h2>
            <form action="{{ route('staff.update', $staffedit->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <!-- Personal Information Section -->
              <div class="form-section mb-4">
                <h6 class="section-title mb-3">
                  <i class="bi bi-person-badge me-2"></i>Personal Information
                </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" value="{{ $staffedit->fullname }}">
                  @error('fullname')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required value="{{ $staffedit->username }}">
                  @error('username')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required value="{{ $staffedit->email }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="{{ $staffedit->phone }}">
                  @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              </div>
              <!-- End Personal Information -->

              <!-- Account Security Section -->
              <div class="form-section mb-4">
                <h6 class="section-title mb-3">
                  <i class="bi bi-shield-lock me-2"></i>Account Security
                </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">Password <span class="text-muted">(Leave blank to keep current password)</span></label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                  <small class="text-muted">Min 8 characters, mix of letters & numbers</small>
                  @error('password')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="password_confirmation" class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                </div>
              </div>
              </div>
              <!-- End Account Security -->

              <!-- Role & Status Section -->
              <div class="form-section mb-4">
                <h6 class="section-title mb-3">
                  <i class="bi bi-briefcase me-2"></i>Role & Status
                </h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                  <select class="form-select" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="Manager" {{ $staffedit->role == 'Manager' ? 'selected' : '' }}>Manager</option>
                    <option value="Sales Staff" {{ $staffedit->role == 'Sales Staff' ? 'selected' : '' }}>Sales Staff</option>
                  </select>
                  @error('role')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-6 mb-2">
                  <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                  <select class="form-select" id="status" name="status" required>
                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }} selected>Active</option>
                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="On Leave" {{ old('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                  </select>
                  <div class="invalid-feedback">Please select a status.</div>
              </div>
              </div>
              </div>
              <!-- End Role & Status -->

              <!-- Profile Photo Section -->
              <div class="form-section mb-3">
                <h6 class="section-title mb-3">
                  <i class="bi bi-camera me-2"></i>Profile Photo <span class="text-muted" style="font-size: 0.85rem; font-weight: normal;">(Optional)</span>
                </h6>
              <div class="row">
                <div class="col-md-12">
                  <div class="photo-upload-area">
                    <input type="file" class="form-control" id="passport_photo" name="passport_photo" accept="image/*">
                    <div id="uploadPlaceholder" class="upload-placeholder">
                      <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: #6c757d;"></i>
                      <p class="mb-2 mt-2"><strong>Click to upload</strong> or drag and drop</p>
                      <small class="text-muted">JPG, PNG, GIF (max 2MB)</small>
                    </div>
                    <div id="photoPreview" class="photo-preview" style="display: none;">
                      <img id="previewImage" src="" alt="Preview">
                      <button type="button" class="btn btn-sm btn-danger remove-photo-btn" id="removePhoto">
                        <i class="bi bi-trash"></i> Remove Photo
                      </button>
                    </div>
                  </div>
                  @error('passport_photo')
                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              </div>
              <!-- End Profile Photo -->

              <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('manager.staff') }}" class="btn btn-secondary">
                  <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-circle me-1"></i>Update Staff Member
                </button>
              </div>
            </form>
          </div>
@endsection
