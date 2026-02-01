@extends('manager.layouts.layout')
@section('manager_page_title')
Edit Staff
@endsection
@section('manager_layout_content')
  <div class="modal-body">

    <h2> Edit Profile</h2>
            <form action="{{ route('manager.update', $manageredit->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <!-- Personal Information Section -->
              <div class="form-section mb-4">
                <h6 class="section-title mb-3">
                  <i class="bi bi-person-badge me-2"></i>Personal Information
                </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="surname" class="form-label">Surname <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter surname" value="{{ $manageredit->surname }}">
                  @error('surname')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                 <div class="col-md-6 mb-3">
                  <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter first name" value="{{ $manageredit->first_name }}">
                  @error('first_name')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-6 mb-3">
                  <label for="other_name" class="form-label">Other Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="other_name" name="other_name" placeholder="Enter other name" required value="{{ $manageredit->other_name }}">
                  @error('other_name')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" readonly value="{{ $manageredit->email }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="{{ $manageredit->phone_number }}">
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
                  <select class="form-select" id="role" name="role">
                    <option value="">Select Role</option>
                    <option value="Manager" {{ $manageredit->role == 'Manager' ? 'selected' : '' }}>Manager</option>
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
                    {{--  <option value="On Leave" {{ old('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>  --}}
                  </select>
                  <div class="invalid-feedback">Please select a status.</div>
              </div>
              </div>
              </div>
              <!-- End Role & Status -->



              <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('manager.manager') }}" class="btn btn-secondary">
                  <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-circle me-1"></i>Update Manager
                </button>
              </div>
            </form>
          </div>
@endsection
