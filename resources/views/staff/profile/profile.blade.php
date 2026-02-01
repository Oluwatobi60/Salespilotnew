@extends('staff.layouts.layout')
@section('staff_page_title')
Staff Profile
@endsection
@section('staff_layout_content')
<link rel="stylesheet" href="{{ asset('staff_asset/css/profile_style.css') }}">

          <div class="content-wrapper">
            <!-- Profile content starts here -->

            <!-- Success and Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
              <div class="col-12">
                <div class="profile-header text-center">
                  @if($staff->passport_photo && file_exists(public_path('uploads/' . $staff->passport_photo)))
                    <img src="{{ asset('uploads/' . $staff->passport_photo) }}" alt="Profile" class="profile-avatar mb-3">
                  @elseif($staff->passport_photo)
                    <img src="{{ asset($staff->passport_photo) }}" alt="Profile" class="profile-avatar mb-3" onerror="this.src='{{ asset('manager_asset/assets/images/faces/face8.jpg') }}'">
                  @else
                    <img src="{{ asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile" class="profile-avatar mb-3">
                  @endif
                  <h3 class="mb-1">{{ $staff->fullname }}</h3>
                  <p class="mb-0">{{ $staff->email }}</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="profile-info-card">
                  <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Personal Information</h5>
                  <div class="info-row">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">{{ $staff->fullname }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $staff->email }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">{{ $staff->phone ?? 'Not provided' }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value"><span class="badge bg-primary">{{ ucfirst($staff->role) }}</span></span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                      @if(strtolower($staff->status) === '1')
                        <span class="badge bg-success">Active</span>
                      @elseif(strtolower($staff->status) === '0')
                        <span class="badge bg-danger">Inactive</span>
                      @else
                        <span class="badge bg-secondary">{{ ucfirst($staff->status) }}</span>
                      @endif
                    </span>
                  </div>
                  @if($staff->address)
                  <div class="info-row">
                    <span class="info-label">Address</span>
                    <span class="info-value">{{ $staff->address }}</span>
                  </div>
                  @endif
                </div>
              </div>

              <div class="col-md-6">
                <div class="profile-info-card">
                  <h5 class="mb-3"><i class="bi bi-building me-2"></i>Work Information</h5>
                  <div class="info-row">
                    <span class="info-label">Employee ID</span>
                    <span class="info-value">{{ $staff->staffsid }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Join Date</span>
                    <span class="info-value">{{ $staff->created_at->format('F d, Y') }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value">{{ $staff->updated_at->format('M d, Y g:i A') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="profile-info-card">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Account Settings</h5>
                    <button class="btn btn-primary btn-sm" id="openPasswordPanel">
                      <i class="bi bi-key me-1"></i>Change Password
                    </button>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Two-Factor Authentication</span>
                    <span class="info-value">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="twoFactorSwitch">
                        <label class="form-check-label" for="twoFactorSwitch">Disabled</label>
                      </div>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Email Notifications</span>
                    <span class="info-value">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        <label class="form-check-label" for="emailNotifications">Enabled</label>
                      </div>
                    </span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Account Created</span>
                    <span class="info-value">{{ $staff->created_at->format('F d, Y') }}</span>
                  </div>

                </div>
              </div>
            </div>
            <!-- Profile content ends here -->
          </div>
          <!-- content-wrapper ends -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">© 2025 SalesPilot. All rights reserved.</span>
            </div>
          </footer>



        <!-- Change Password Side Panel -->
    <div class="password-panel" id="passwordPanel">
      <div class="panel-overlay" id="panelOverlay"></div>
      <div class="panel-content">
        <div class="panel-header">
          <h5 class="panel-title">
            <i class="bi bi-key me-2"></i>Change Password
          </h5>
          <button type="button" class="btn-close-panel" id="closePanelBtn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <div class="panel-body">
          <div id="passwordMessage" class="alert" style="display: none;"></div>
          <form id="changePasswordForm" method="POST" action="{{ route('staff.update.password') }}">
            @csrf
            <div class="mb-3">
              <label for="currentPassword" class="form-label">Current Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <div class="mb-3">
              <label for="newPassword" class="form-label">New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="newPassword" name="new_password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <small class="form-text text-muted">Password must be at least 8 characters long</small>
            </div>
            <div class="mb-3">
              <label for="confirmPassword" class="form-label">Confirm New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
          </form>
        </div>

        <div class="panel-footer">
          <button type="button" class="btn btn-secondary" id="closePanelFooterBtn">Cancel</button>
          <button type="submit" form="changePasswordForm" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Update Password
          </button>
        </div>
      </div>
    </div>


<script src="{{ asset('staff_asset/js/profile.js') }}"></script>
@endsection
