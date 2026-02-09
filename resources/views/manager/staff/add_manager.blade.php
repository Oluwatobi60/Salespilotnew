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
                          <i class="bi bi-person-workspace me-2"></i>Manage Managers
                        </h4>
                        <p class="card-description mb-0">Manage your manager members and their roles</p>
                      </div>
                      <button type="button" class="btn btn-primary" style="min-width: 150px;" id="openAddStaffBtn"><strong>+ Add Manager</strong></button>

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
                            <th>Branch & Location</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                           @foreach ($delegatedManagers as $manager)
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
                                <img src="  {{ $manager->passport_photo ? asset($manager->passport_photo)   : asset('manager_asset/images/faces/face1.jpg') }}" alt="Profile" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0"> {{ $manager->fullname }}  </h6>
                                  <p class="text-muted mb-0">{{ 'Name: ' . $manager->surname. ' ' . $manager->first_name }}  </p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="badge bg-primary">{{ $manager->role }} </span>
                            </td>
                            <td>
                              <div>
                                <p class="mb-1">{{ $manager->email }}  </p>
                                <p class="text-muted mb-0">{{ $manager->phone_number }}  </p>
                              </div>
                            </td>
                            <td>
                              <div>
                                @if($manager->managedBranch)
                                  <p class="mb-1"><strong>{{ $manager->managedBranch->branch_name }}</strong></p>
                                  <p class="text-muted mb-0 small">{{ $manager->managedBranch->address }}, {{ $manager->managedBranch->local_govt }}</p>
                                @else
                                  <span class="badge bg-secondary">No Branch</span>
                                @endif
                              </div>
                            </td>
                              <td>
                              <span class="badge bg-success">{{ $manager->status ? 'Active' : 'Inactive' }} </span>
                            </td>
                            <td>
                              <p class="mb-0">{{ $manager->created_at->format('M d, Y') }}  </p>
                            </td>
                            <td>
                              <div class="d-flex justify-content-center align-items-center flex-nowrap mx-auto" style="gap: 3rem;">
                                <!-- Edit Button -->
                                <a href="{{ route('manager.edit', $manager->id) }}" class="btn btn-sm btn-info text-white d-flex align-items-center" title="Edit Details">
                                  <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Enable/Disable Switch -->
                                <form action="{{ route('manager.toggle_status', $manager->id) }}" method="POST" class="d-flex align-items-center m-0 p-0">
                                  @csrf
                                  @method('PATCH')
                                  <div class="form-check form-switch d-flex align-items-center m-0">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch{{ $manager->id }}" name="status"
                                      onchange="this.form.submit()" {{ ($manager->status == 1 || $manager->status == 'Active') ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1 mb-0" for="statusSwitch{{ $manager->id }}">
                                      {{ ($manager->status == 1 || $manager->status == 'Active') ? 'Enabled' : 'Disabled' }}
                                    </label>
                                  </div>
                                </form>
                                <!-- Delete Button -->
                                <form action="{{ route('manager.delete', $manager->id) }}" method="POST" class="delete-staff-form d-flex align-items-center m-0 p-0">
                                  @csrf
                                  @method('DELETE')
                                  <button type="button" class="btn btn-sm btn-danger delete-staff-btn d-flex align-items-center"
                                          data-staff-name="{{ $manager->first_name ?? $manager->fullname }}">
                                      <i class="bi bi-trash"></i>
                                  </button>
                                </form>
                              </div>
                            </td>
                          </tr>
                            @endforeach  -



                        </tbody>
                      </table>
                    </div>

                      <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{--  {{ $staffdata->firstItem() ?? 0 }}-{{ $staffdata->lastItem() ?? 0 }}  --}}</strong> of <strong>{{--  {{ $staffdata->total() }}  --}}</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        {{--  {{ $staffdata->links('pagination::bootstrap-5') }}  --}}
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
    </div>



<!-- Add Manager Side Panel - Outside section to cover entire viewport -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>
<div class="side-panel" id="addManagerPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-person-plus me-2"></i>Add New Manager Member
      </h5>
      <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
            <form id="addManagerForm" action="{{ route('manager.create') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <!-- Business Name -->
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Enter business name" required value="{{ old('business_name', Auth::user()->business_name ?? '') }}" readonly>
                  @error('business_name')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <!-- Business Logo -->
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="business_logo" class="form-label">Business Logo</label>
                  <input type="file" class="form-control" id="business_logo" name="business_logo" accept="image/*">
                  @error('business_logo')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <!-- Personal Information Section -->
              <div class="form-section mb-4">

                <h6 class="section-title mb-3">
                  <i class="bi bi-person-badge me-2"></i>Personal Information
                </h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="firstname" class="form-label">First Name: <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Enter first name" required value="{{ old('firstname') }}">
                  @error('firstname')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
                <div class="col-md-6 mb-3">
                  <label for="surname" class="form-label">Surname <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="surname" name="surname" placeholder="Enter surname" required value="{{ old('surname') }}">
                  @error('surname')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

               <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="othername" class="form-label">Othername: <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="othername" name="othername" placeholder="Enter othername" required value="{{ old('othername') }}">
                  @error('othername')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
               {{--   <div class="col-md-6 mb-3">
                  <label for="staff_id" class="form-label">Staff ID <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="staff_id" name="staff_id" placeholder="Auto-generated" value="{{ old('staff_id') }}" readonly required>
                  <small class="text-muted">Auto-generated: 3 letters of surname + 3 digits</small>
                  @error('staff_id')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>  --}}
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
                <div class="col-md-6 mb-2">
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="5" cols="5" placeholder="Enter address (optional)"></textarea>
                </div>

                 <div class="col-md-6 mb-2">

                     <div class="input_box">
                         <label for="address" class="form-label">State:</label>
                        <select id="stateSelect" name="state" required class="form-select">
                            <option value="">Select State</option>
                            <option value="Abia" {{ old('state') == 'Abia' ? 'selected' : '' }}>Abia</option>
                            <option value="Adamawa" {{ old('state') == 'Adamawa' ? 'selected' : '' }}>Adamawa</option>
                            <option value="Akwa Ibom" {{ old('state') == 'Akwa Ibom' ? 'selected' : '' }}>Akwa Ibom</option>
                            <option value="Anambra" {{ old('state') == 'Anambra' ? 'selected' : '' }}>Anambra</option>
                            <option value="Bauchi" {{ old('state') == 'Bauchi' ? 'selected' : '' }}>Bauchi</option>
                            <option value="Bayelsa" {{ old('state') == 'Bayelsa' ? 'selected' : '' }}>Bayelsa</option>
                            <option value="Benue" {{ old('state') == 'Benue' ? 'selected' : '' }}>Benue</option>
                            <option value="Borno" {{ old('state') == 'Borno' ? 'selected' : '' }}>Borno</option>
                            <option value="Cross River" {{ old('state') == 'Cross River' ? 'selected' : '' }}>Cross River</option>
                            <option value="Delta" {{ old('state') == 'Delta' ? 'selected' : '' }}>Delta</option>
                            <option value="Ebonyi" {{ old('state') == 'Ebonyi' ? 'selected' : '' }}>Ebonyi</option>
                            <option value="Edo" {{ old('state') == 'Edo' ? 'selected' : '' }}>Edo</option>
                            <option value="Ekiti" {{ old('state') == 'Ekiti' ? 'selected' : '' }}>Ekiti</option>
                            <option value="Enugu" {{ old('state') == 'Enugu' ? 'selected' : '' }}>Enugu</option>
                            <option value="FCT" {{ old('state') == 'FCT' ? 'selected' : '' }}>FCT - Abuja</option>
                            <option value="Gombe" {{ old('state') == 'Gombe' ? 'selected' : '' }}>Gombe</option>
                            <option value="Imo" {{ old('state') == 'Imo' ? 'selected' : '' }}>Imo</option>
                            <option value="Jigawa" {{ old('state') == 'Jigawa' ? 'selected' : '' }}>Jigawa</option>
                            <option value="Kaduna" {{ old('state') == 'Kaduna' ? 'selected' : '' }}>Kaduna</option>
                            <option value="Kano" {{ old('state') == 'Kano' ? 'selected' : '' }}>Kano</option>
                            <option value="Katsina" {{ old('state') == 'Katsina' ? 'selected' : '' }}>Katsina</option>
                            <option value="Kebbi" {{ old('state') == 'Kebbi' ? 'selected' : '' }}>Kebbi</option>
                            <option value="Kogi" {{ old('state') == 'Kogi' ? 'selected' : '' }}>Kogi</option>
                            <option value="Kwara" {{ old('state') == 'Kwara' ? 'selected' : '' }}>Kwara</option>
                            <option value="Lagos" {{ old('state') == 'Lagos' ? 'selected' : '' }}>Lagos</option>
                            <option value="Nasarawa" {{ old('state') == 'Nasarawa' ? 'selected' : '' }}>Nasarawa</option>
                            <option value="Niger" {{ old('state') == 'Niger' ? 'selected' : '' }}>Niger</option>
                            <option value="Ogun" {{ old('state') == 'Ogun' ? 'selected' : '' }}>Ogun</option>
                            <option value="Ondo" {{ old('state') == 'Ondo' ? 'selected' : '' }}>Ondo</option>
                            <option value="Osun" {{ old('state') == 'Osun' ? 'selected' : '' }}>Osun</option>
                            <option value="Oyo" {{ old('state') == 'Oyo' ? 'selected' : '' }}>Oyo</option>
                            <option value="Plateau" {{ old('state') == 'Plateau' ? 'selected' : '' }}>Plateau</option>
                            <option value="Rivers" {{ old('state') == 'Rivers' ? 'selected' : '' }}>Rivers</option>
                            <option value="Sokoto" {{ old('state') == 'Sokoto' ? 'selected' : '' }}>Sokoto</option>
                            <option value="Taraba" {{ old('state') == 'Taraba' ? 'selected' : '' }}>Taraba</option>
                            <option value="Yobe" {{ old('state') == 'Yobe' ? 'selected' : '' }}>Yobe</option>
                            <option value="Zamfara" {{ old('state') == 'Zamfara' ? 'selected' : '' }}>Zamfara</option>
                        </select>
          <i class="uil uil-map"></i>
        </div>
                 </div>
              </div>



            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                           <div class="input_box">
                             <label for="address" class="form-label">Local Government Area:</label>
                                <select id="lgaSelect" name="local_govt" required class="form-select">
                                    <option value="">Select Local Government Area</option>
                                </select>
                                <i class="uil uil-map-marker"></i>
                          </div>
                </div>


              {{--    <div class="col-md-6 mb-2">
                           <div class="input_box">
                             <label for="address" class="form-label">Brance name:</label>
                                <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Enter branch name" required>
                                <i class="uil uil-map-marker"></i>
                          </div>
                </div>  --}}
            </div>

              <!-- Profile Photo Section -->
            {{--    <div class="form-section mb-3">
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
              </div>  --}}
              <!-- End Profile Photo -->
            </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelAddStaff">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="addManagerForm" id="addManagerBtn" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i>Add Manager Member
      </button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('manager_asset/js/manager.js') }}"></script>
<script src="{{ asset('welcome_asset/js/register_lg.js') }}"></script>
<script>
// SweetAlert2 for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtns = document.querySelectorAll('.delete-staff-btn');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const staffName = this.getAttribute('data-staff-name');
            const form = this.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${staffName}"? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>


@endsection
