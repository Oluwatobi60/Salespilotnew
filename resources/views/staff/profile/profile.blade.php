@extends('staff.layouts.layout')
@section('staff_page_title')
Completed Sales
@endsection
@section('staff_layout_content')
<link rel="stylesheet" href="{{ asset('staff_asset/css/profile_style.css') }}">

  <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">

        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <!-- Profile content starts here -->
            <div class="row">
              <div class="col-12">
                <div class="profile-header text-center">
                  <img src="../Manager/assets/images/faces/face8.jpg" alt="Profile" class="profile-avatar mb-3">
                  <h3 class="mb-1">Staff User</h3>
                  <p class="mb-0">staff@salespilot.com</p>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="profile-info-card">
                  <h5 class="mb-3"><i class="bi bi-person-circle me-2"></i>Personal Information</h5>
                  <div class="info-row">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">Staff User</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">staff@salespilot.com</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Phone</span>
                    <span class="info-value">+234 800 000 0000</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value"><span class="badge bg-primary">Staff</span></span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value"><span class="badge bg-success">Active</span></span>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="profile-info-card">
                  <h5 class="mb-3"><i class="bi bi-building me-2"></i>Work Information</h5>
                  <div class="info-row">
                    <span class="info-label">Department</span>
                    <span class="info-value">Sales</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Employee ID</span>
                    <span class="info-value">EMP-001</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Join Date</span>
                    <span class="info-value">January 15, 2024</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Last Login</span>
                    <span class="info-value">December 2, 2025 10:30 AM</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Total Sales</span>
                    <span class="info-value text-success">₦1,250,000</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="profile-info-card">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Account Settings</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
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
                    <span class="info-value">January 15, 2024</span>
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
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>


        <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="changePasswordForm">
            <div class="modal-body">
              <div class="mb-3">
                <label for="currentPassword" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" required>
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" required>
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>


<script src="{{ asset('staff_asset/js/profile.js') }}"></script>
@endsection
