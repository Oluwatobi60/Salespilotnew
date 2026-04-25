@extends('manager.layouts.layout')
@section('manager_page_title')
Manager Profile
@endsection
@section('manager_layout_content')

<link rel="stylesheet" href="{{ asset('manager_asset/css/showprofile.css') }}">

<div class="content-wrapper">
    <!-- Cover Banner -->
    <div class="profile-cover"></div>

    <div class="container-fluid px-4">
        @php $manager = isset($manager) ? $manager : Auth::user(); @endphp

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

        <!-- Avatar & Header Info -->
        <div class="profile-avatar-container">
            @if($manager->business_logo)
                <img src="{{ asset('storage/' . $manager->business_logo) }}" alt="Business Logo" class="profile-avatar">
            @else
                <img src="{{ asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile" class="profile-avatar">
            @endif
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-header-info">
                    <h1 class="profile-name">{{ $manager->first_name }} {{ $manager->other_name }} {{ $manager->surname }}</h1>
                    <p class="profile-email"><i class="bi bi-envelope me-2"></i>{{ $manager->email }}</p>
                    <div class="profile-actions">
                        <button class="btn btn-modern btn-primary-modern" id="openEditPanel">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </button>
                        <button class="btn btn-modern btn-secondary-modern" id="openPasswordPanel">
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
                                <div class="info-card-icon icon-purple">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <h3 class="info-card-title">Personal Information</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person"></i>Full Name</span>
                                <span class="info-value">{{ $manager->first_name }} {{ $manager->other_name }} {{ $manager->surname }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-envelope"></i>Email</span>
                                <span class="info-value">{{ $manager->email }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-telephone"></i>Phone</span>
                                <span class="info-value">{{ $manager->phone_number ?? 'Not provided' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-briefcase"></i>Business</span>
                                <span class="info-value">{{ $manager->business_name }}</span>
                            </div>
                            @if($manager->addby)
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-building"></i>Branch</span>
                                <span class="info-value">{{ $manager->branch_name ?? ($manager->managedBranch ? $manager->managedBranch->branch_name : 'Not assigned') }}</span>
                            </div>
                            @endif
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-geo-alt"></i>Address</span>
                                <span class="info-value">{{ $manager->address ?? '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-check"></i>Joined</span>
                                <span class="info-value">{{ $manager->created_at ? $manager->created_at->format('M d, Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Information Card -->
                    @if(!$manager->addby)
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-blue">
                                    <i class="bi bi-building"></i>
                                </div>
                                <h3 class="info-card-title">Subscription</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-award"></i>Plan</span>
                                <span class="info-value">{{ $plan ? $plan->name : 'No active subscription' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-calendar-event"></i>Expires</span>
                                <span class="info-value">{{ $subscription ? $subscription->end_date->format('M d, Y') : '-' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-shield-check"></i>Status</span>
                                <span class="info-value">
                                    @if($subscription && $subscription->isActive())
                                        <span class="badge-modern badge-success-modern">Active</span>
                                    @else
                                        <span class="badge-modern badge-danger-modern">Expired</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- BRM Contact Card -->
                        @if($manager->brm)
                        <div class="info-card">
                            <div class="info-card-header">
                                <div class="info-card-icon icon-green">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <h3 class="info-card-title">Your BRM</h3>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-person"></i>Name</span>
                                <span class="info-value">{{ $manager->brm->name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-envelope"></i>Email</span>
                                <span class="info-value">{{ $manager->brm->email ?? 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label"><i class="bi bi-telephone"></i>Phone</span>
                                <span class="info-value">{{ $manager->brm->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Side Panel -->
    <div class="edit-panel" id="editPanel" style="display:none;">
      <div class="panel-overlay" id="editPanelOverlay"></div>
      <div class="panel-content">
        <div class="panel-header">
          <h5 class="panel-title">
            <i class="bi bi-pencil-square me-2"></i>Edit Profile
          </h5>
          <button type="button" class="btn-close-panel" id="closeEditPanelBtn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="panel-body">
          @if($errors->any() && session('panel') === 'edit')
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <form method="POST" action="{{ route('manager.profile.update') }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label for="firstname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstname" name="firstname" value="{{ old('firstname', $manager->first_name) }}" required>
            </div>
            <div class="mb-3">
              <label for="othername" class="form-label">Other Name</label>
              <input type="text" class="form-control" id="othername" name="othername" value="{{ old('othername', $manager->other_name) }}">
            </div>
            <div class="mb-3">
              <label for="surname" class="form-label">Surname</label>
              <input type="text" class="form-control" id="surname" name="surname" value="{{ old('surname', $manager->surname) }}" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $manager->email) }}" readonly>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $manager->phone_number) }}">
            </div>
            <div class="mb-3">
              <label for="business_name" class="form-label">Business Name</label>
              <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $manager->business_name) }}" readonly>
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $manager->address) }}">
            </div>
            <div class="profile-btns">
                <button type="button" class="btn btn-secondary" id="cancelEditPanelBtn">Cancel</button>
                <button type="submit" class="btn btn-modern btn-primary-modern">
                    <i class="bi bi-check-lg"></i> Update Profile
                </button>
            </div>
          </form>
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
          @if($errors->any() && session('panel') === 'password')
            <div class="alert alert-danger alert-modern">
              <strong><i class="bi bi-exclamation-circle me-2"></i>Please fix the following errors:</strong>
              <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="password-info-box mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <span>Choose a strong password to keep your account secure</span>
          </div>

          <form method="POST" action="{{ route('manager.profile.change_password.post') }}" id="changePasswordForm">
            @csrf

            <!-- Current Password -->
            <div class="mb-4">
              <label for="current_password" class="form-label">
                <i class="bi bi-key text-primary me-2"></i>Current Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="current_password"
                       name="current_password"
                       placeholder="Enter your current password"
                       required>
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('current_password')">
                  <i class="bi bi-eye" id="current_password_icon"></i>
                </button>
              </div>
            </div>

            <!-- New Password -->
            <div class="mb-4">
              <label for="new_password" class="form-label">
                <i class="bi bi-lock text-success me-2"></i>New Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="new_password"
                       name="new_password"
                       placeholder="Enter new password"
                       required
                       oninput="checkPasswordStrength(this.value)">
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('new_password')">
                  <i class="bi bi-eye" id="new_password_icon"></i>
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
              <label for="new_password_confirmation" class="form-label">
                <i class="bi bi-check-circle text-info me-2"></i>Confirm New Password
              </label>
              <div class="password-input-wrapper">
                <input type="password"
                       class="form-control form-control-modern"
                       id="new_password_confirmation"
                       name="new_password_confirmation"
                       placeholder="Re-enter new password"
                       required
                       oninput="checkPasswordMatch()">
                <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('new_password_confirmation')">
                  <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                </button>
              </div>
              <small class="password-match-text mt-2" id="passwordMatchText"></small>
            </div>

            <!-- Action Buttons -->
            <div class="profile-btns">
                <button type="button" class="btn btn-light btn-cancel" id="cancelPasswordPanelBtn">
                  <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <button type="submit" class="btn btn-modern btn-secondary-modern" id="submitPasswordBtn">
                    <i class="bi bi-shield-check me-2"></i>Update Password
                </button>
            </div>
          </form>
        </div>
      </div>
    </div>



</div>
<script src="{{ asset('manager_asset/js/profile.js') }}"></script>
@endsection
