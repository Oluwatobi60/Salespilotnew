@extends('staff.layouts.layout')
@section('staff_page_title')
Staff Profile
@endsection
@section('staff_layout_content')
<link rel="stylesheet" href="{{ asset('staff_asset/css/staffprofile.css') }}">

<div class="content-wrapper">
    <!-- Cover Banner -->
    <div class="profile-cover"></div>

    <div class="container-fluid px-4">
        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-modern alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Avatar & Header Info -->
        <div class="profile-avatar-container">
            @if($staff->passport_photo && file_exists(public_path('uploads/' . $staff->passport_photo)))
                <img src="{{ asset('uploads/' . $staff->passport_photo) }}" alt="Profile" class="profile-avatar">
            @elseif($staff->passport_photo)
                <img src="{{ asset($staff->passport_photo) }}" alt="Profile" class="profile-avatar" onerror="this.src='{{ asset('manager_asset/assets/images/faces/face8.jpg') }}'">
            @else
                <img src="{{ asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile" class="profile-avatar">
            @endif
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-header-info">
                    <h1 class="profile-name">{{ $staff->fullname }}</h1>
                    <p class="profile-email"><i class="bi bi-envelope me-2"></i>{{ $staff->email }}</p>
                    <div class="profile-actions">
                        <button class="btn btn-modern btn-primary-modern" id="openPasswordPanel">
                            <i class="bi bi-key"></i> Change Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-10">
                <div class="row">
                    <!-- Personal Information Card -->
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-blue">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <h3 class="info-card-title">Personal Information</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person"></i>Full Name</span>
                                <span class="info-value">{{ $staff->fullname }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-envelope"></i>Email</span>
                                <span class="info-value">{{ $staff->email }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-telephone"></i>Phone</span>
                                <span class="info-value">{{ $staff->phone ?? 'Not provided' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-briefcase"></i>Role</span>
                                <span class="info-value"><span class="badge-modern badge-primary-modern">{{ ucfirst($staff->role) }}</span></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-shield-check"></i>Status</span>
                                <span class="info-value">
                                    @if($staff->status == 1 || $staff->status == '1' || strtolower($staff->status) == 'active')
                                        <span class="badge-modern badge-success-modern">Active</span>
                                    @else
                                        <span class="badge-modern badge-danger-modern">Inactive</span>
                                    @endif
                                </span>
                            </div>
                            @if($staff->address)
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-geo-alt"></i>Address</span>
                                <span class="info-value">{{ $staff->address }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Work Information Card -->
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-purple">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h3 class="info-card-title">Work Information</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-badge-tm"></i>Employee ID</span>
                                <span class="info-value">{{ $staff->staffsid }}</span>
                            </div>
                            @if($plan && !in_array(strtolower($plan->name), ['basic', 'free']))
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-building"></i>Branch</span>
                                <span class="info-value">{{ $staff->branch ? $staff->branch->branch_name : 'Not assigned' }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-check"></i>Join Date</span>
                                <span class="info-value">{{ $staff->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-clock-history"></i>Last Updated</span>
                                <span class="info-value">{{ $staff->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <!-- Account Settings Card -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-green">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h3 class="info-card-title">Security</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-event"></i>Account Created</span>
                                <span class="info-value">{{ $staff->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <!-- Change Password Side Panel -->
    <div class="password-panel" id="passwordPanel" style="display:none;">
      <div class="panel-overlay" id="panelOverlay"></div>
      <div class="panel-content">
        <div class="panel-header">
          <h5 class="panel-title">
            <i class="bi bi-shield-lock me-2"></i>Change Password
          </h5>
          <button type="button" class="btn-close-panel" id="closePanelBtn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="panel-body">
          <div id="passwordMessage" class="alert" style="display: none;"></div>

          <div class="password-info-box mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <span>Choose a strong password to keep your account secure</span>
          </div>

          <form id="changePasswordForm" method="POST" action="{{ route('staff.update.password') }}">
            @csrf

            <!-- Current Password -->
            <div class="mb-4">
              <label for="currentPassword" class="form-label">
                <i class="bi bi-key text-primary me-2"></i>Current Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="currentPassword"
                       name="current_password"
                       placeholder="Enter your current password"
                       required>
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('currentPassword')">
                  <i class="bi bi-eye" id="currentPassword_icon"></i>
                </button>
              </div>
            </div>

            <!-- New Password -->
            <div class="mb-4">
              <label for="newPassword" class="form-label">
                <i class="bi bi-lock text-success me-2"></i>New Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="newPassword"
                       name="new_password"
                       placeholder="Enter new password"
                       required
                       oninput="checkPasswordStrength(this.value)">
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('newPassword')">
                  <i class="bi bi-eye" id="newPassword_icon"></i>
                </button>
              </div>

              <!-- Password Strength Indicator -->
              <div class="password-strength-container mt-2">
                <div class="password-strength-bar">
                  <div class="password-strength-progress" id="passwordStrengthBar"></div>
                </div>
                <small class="password-strength-text" id="passwordStrengthText">Password strength: <span>Not set</span></small>
              </div>

              <!-- Password Requirements -->
              <div class="password-requirements mt-3">
                <small class="text-muted d-block mb-2"><strong>Password must contain:</strong></small>
                <div class="requirement-item" id="req-length">
                  <i class="bi bi-circle"></i> At least 8 characters
                </div>
                <div class="requirement-item" id="req-uppercase">
                  <i class="bi bi-circle"></i> One uppercase letter
                </div>
                <div class="requirement-item" id="req-lowercase">
                  <i class="bi bi-circle"></i> One lowercase letter
                </div>
                <div class="requirement-item" id="req-number">
                  <i class="bi bi-circle"></i> One number
                </div>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
              <label for="confirmPassword" class="form-label">
                <i class="bi bi-check-circle text-info me-2"></i>Confirm New Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="confirmPassword"
                       name="new_password_confirmation"
                       placeholder="Re-enter new password"
                       required
                       oninput="checkPasswordMatch()">
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('confirmPassword')">
                  <i class="bi bi-eye" id="confirmPassword_icon"></i>
                </button>
              </div>
              <small class="password-match-text mt-2" id="passwordMatchText"></small>
            </div>
          </form>
        </div>

        <div class="panel-footer">
          <button type="button" class="btn btn-light btn-cancel" id="closePanelFooterBtn">
            <i class="bi bi-x-circle me-2"></i>Cancel
          </button>
          <button type="submit" form="changePasswordForm" class="btn btn-modern btn-secondary-modern">
            <i class="bi bi-shield-check me-2"></i>Update Password
          </button>
        </div>
      </div>
    </div>

    <style>
    /* Side Panels */
    .password-panel {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        display: none;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .password-panel.active {
        display: flex !important;
    }

    .panel-content {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .panel-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 20px 20px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-close-panel {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-close-panel:hover {
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }

    .panel-body {
        padding: 2rem;
    }

    .panel-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    /* Password Panel Enhancements */
    .password-info-box {
        background: linear-gradient(135deg, #e8f4f8 0%, #f0f8ff 100%);
        border-left: 4px solid #4facfe;
        padding: 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #2d3748;
    }

    .password-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .form-control-modern {
        padding-right: 45px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        border-color: #4facfe;
        box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }

    .password-toggle-btn {
        position: absolute;
        right: 10px;
        background: none;
        border: none;
        color: #718096;
        cursor: pointer;
        padding: 5px 10px;
        transition: color 0.3s ease;
        z-index: 10;
    }

    .password-toggle-btn:hover {
        color: #4facfe;
    }

    /* Password Strength Indicator */
    .password-strength-container {
        margin-top: 0.75rem;
    }

    .password-strength-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .password-strength-progress {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 10px;
    }

    .password-strength-progress.weak {
        width: 33%;
        background: linear-gradient(90deg, #f56565, #fc8181);
    }

    .password-strength-progress.medium {
        width: 66%;
        background: linear-gradient(90deg, #ed8936, #f6ad55);
    }

    .password-strength-progress.strong {
        width: 100%;
        background: linear-gradient(90deg, #48bb78, #68d391);
    }

    .password-strength-text {
        color: #718096;
        font-size: 0.85rem;
    }

    .password-strength-text span {
        font-weight: 600;
    }

    /* Password Requirements */
    .password-requirements {
        background: #f7fafc;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .requirement-item {
        font-size: 0.85rem;
        color: #718096;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .requirement-item i {
        font-size: 0.7rem;
    }

    .requirement-item.met {
        color: #48bb78;
        font-weight: 600;
    }

    .requirement-item.met i::before {
        content: "\f26b";
        font-family: "bootstrap-icons";
    }

    .password-match-text {
        display: block;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 600;
    }

    .password-match-text.match {
        color: #48bb78;
    }

    .password-match-text.no-match {
        color: #f56565;
    }

    .btn-cancel {
        background: #f7fafc;
        color: #718096;
        border: 2px solid #e2e8f0;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #e2e8f0;
        color: #2d3748;
    }
    </style>

    <script>
    // Toggle password visibility
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Check password strength
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');

        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);

        // Update requirement indicators
        updateRequirement('req-length', hasLength);
        updateRequirement('req-uppercase', hasUpper);
        updateRequirement('req-lowercase', hasLower);
        updateRequirement('req-number', hasNumber);

        // Calculate strength
        let strength = 0;
        if (hasLength) strength++;
        if (hasUpper) strength++;
        if (hasLower) strength++;
        if (hasNumber) strength++;
        if (password.length >= 12) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        // Update strength bar
        strengthBar.className = 'password-strength-progress';
        if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.innerHTML = 'Password strength: <span style="color: #f56565;">Weak</span>';
        } else if (strength <= 4) {
            strengthBar.classList.add('medium');
            strengthText.innerHTML = 'Password strength: <span style="color: #ed8936;">Medium</span>';
        } else {
            strengthBar.classList.add('strong');
            strengthText.innerHTML = 'Password strength: <span style="color: #48bb78;">Strong</span>';
        }

        // Check password match if confirm field has value
        checkPasswordMatch();
    }

    // Update requirement indicator
    function updateRequirement(id, met) {
        const element = document.getElementById(id);
        if (met) {
            element.classList.add('met');
        } else {
            element.classList.remove('met');
        }
    }

    // Check if passwords match
    function checkPasswordMatch() {
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        const matchText = document.getElementById('passwordMatchText');

        if (confirmPassword.length === 0) {
            matchText.textContent = '';
            matchText.className = 'password-match-text';
            return;
        }

        if (newPassword === confirmPassword) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'password-match-text match';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'password-match-text no-match';
        }
    }
    </script>

</div>
<script src="{{ asset('staff_asset/js/profile.js') }}"></script>
@endsection
