@extends('manager.layouts.layout')
@section('manager_page_title')
Add Staff Member
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/staff_style.css') }}">
<link rel="stylesheet" href="{{ asset('manager_asset/css/staffs_style.css') }}">

    <div class="content-wrapper d-flex" id="staffContentWrapper">
             <!-- Staff Management Content -->
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Staff Management</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Validation Error!</strong> Please correct the following:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mt-4">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <div>
                        <h4 class="card-title mb-2">
                          <i class="bi bi-person-workspace me-2"></i>Staff Members
                        </h4>
                        <p class="card-description mb-0">Manage your staff members and their roles</p>
                      </div>
                      <button type="button" class="btn btn-primary" style="min-width: 150px;" id="openAddStaffBtn"><strong>+ Add Staff</strong></button>
                </div>

 <!-- Search and Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control border-start-0" placeholder="Search by name, email, or ID..." id="searchInput">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <select class="form-select" id="roleFilter">
                          <option value="">All Roles</option>
                          <option value="manager">manager</option>
                          <option value="staff">staff</option>
                        </select>
                      </div>
                    </div>

                     <div class="table-responsive mt-1">
                      <table class="table select-table" id="staffsTable">
                        <thead>
                          <tr>
                            <th>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false" id="check-all">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </th>
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach ($staffdata as $staff)
                          <!-- Sample Data -->
                           <tr>
                            <td>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="{{ $staff->passport_photo ? asset($staff->passport_photo) : asset('manager_asset/images/faces/face1.jpg') }}" alt="Profile" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0">{{ $staff->fullname }}</h6>
                                  <p class="text-muted mb-0">{{ '@' . $staff->username }}</p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="badge bg-primary">{{ $staff->role }}</span>
                            </td>
                            <td>
                              <div>
                                <p class="mb-1">{{ $staff->email }}</p>
                                <p class="text-muted mb-0">{{ $staff->phone }}</p>
                              </div>
                            </td>
                            <td>
                              <p class="mb-0">{{ $staff->created_at->format('M d, Y') }}</p>
                            </td>
                            <td>

                              <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-sm btn-info text-white me-1" title="Edit Details">
                                <i class="bi bi-pencil"></i>
                              </a>

                            <form action="{{ route('staff.delete', $staff->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this Staff?')"> <i class="bi bi-trash"></i></button>
                            </form>
                            </td>
                          </tr>
                            @endforeach



                        </tbody>
                      </table>
                    </div>

                      <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $staffdata->firstItem() ?? 0 }}-{{ $staffdata->lastItem() ?? 0 }}</strong> of <strong>{{ $staffdata->total() }}</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        {{ $staffdata->links('pagination::bootstrap-5') }}
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
    </div>



<!-- Add Staff Side Panel - Outside section to cover entire viewport -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>
<div class="side-panel" id="addStaffPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-person-plus me-2"></i>Add New Staff Member
      </h5>
      <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
            <form id="addStaffForm" action="{{ route('staff.create') }}" method="POST" enctype="multipart/form-data">
              @csrf

              <!-- Personal Information Section -->
              <div class="form-section mb-4">
                <h6 class="section-title mb-3">
                  <i class="bi bi-person-badge me-2"></i>Personal Information
                </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" required value="{{ old('fullname') }}">
                  @error('fullname')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required value="{{ old('username') }}">
                  @error('username')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required value="{{ old('email') }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
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
                  <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                  <small class="text-muted">Min 8 characters, mix of letters & numbers</small>
                  @error('password')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
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
                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
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


              <div class="row mb-3">
                <div class="col-md-12 mb-2">
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="5" cols="5" placeholder="Enter address (optional)"></textarea>
                </div>
              </div>

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
            </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelAddStaff">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="addStaffForm" id="addStaffBtn" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Add Staff Member
      </button>
    </div>
  </div>
</div>

<script src="{{ asset('manager_asset/js/staff.js') }}"></script>

@endsection
