@extends('manager.layouts.layout')
@section('manager_page_title')
Manager Profile
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/profile_style.css') }}">

<div class="content-wrapper">
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
            <div class="profile-header text-center position-relative">
                @php $manager = isset($manager) ? $manager : Auth::user(); @endphp
                {{-- Debug: Show business_logo value and path --}}
                {{--  <div style="font-size:12px;color:#c00;background:#fffbe6;padding:2px 8px;margin-bottom:4px;display:inline-block;">
                    business_logo: {{ $manager->business_logo ?? 'NULL' }}<br>
                    path: {{ $manager->business_logo ? asset('storage/' . $manager->business_logo) : 'default avatar' }}
                </div>  --}}
                @if($manager->business_logo)
                    <img src="{{ asset('storage/' . $manager->business_logo) }}" alt="Business Logo" class="profile-avatar mb-3">
                @else
                    <img src="{{ asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile" class="profile-avatar mb-3">
                @endif
                <h3 class="mb-1">{{ $manager->first_name }} {{ $manager->other_name }} {{ $manager->surname }}</h3>
                <p class="mb-0">{{ $manager->email }}</p>
                <div class="profile-btns position-absolute top-0 end-0 mt-3 me-3">
                    <button class="btn btn-outline-primary" id="openEditPanel">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </button>
                    <button class="btn btn-outline-warning" id="openPasswordPanel">
                        <i class="bi bi-key"></i> Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <div class="profile-info-card text-center">
                <h5><i class="bi bi-person-circle me-2"></i>Personal Information</h5>
                <div class="info-row"><span class="info-label"><i class="bi bi-person"></i>Full Name</span><span class="info-value">{{ $manager->first_name }} {{ $manager->other_name }} {{ $manager->surname }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-envelope"></i>Email</span><span class="info-value">{{ $manager->email }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-telephone"></i>Phone</span><span class="info-value">{{ $manager->phone_number ?? 'Not provided' }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-briefcase"></i>Business Name</span><span class="info-value">{{ $manager->business_name }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-geo-alt"></i>Address</span><span class="info-value">{{ $manager->address ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-calendar-check"></i>Account Created</span><span class="info-value">{{ $manager->created_at ? $manager->created_at->format('F d, Y') : '-' }}</span></div>
            </div>
        </div>
        @if(!$manager->addby)
        <div class="col-md-6 col-12">
            <div class="profile-info-card">
                <h5><i class="bi bi-building me-2"></i>Subscription Information</h5>
                <div class="info-row"><span class="info-label"><i class="bi bi-award"></i>Current Plan</span><span class="info-value">{{ $plan ? $plan->name : 'No active subscription' }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-calendar-event"></i>Expiry Date</span><span class="info-value">{{ $subscription ? $subscription->end_date->format('F d, Y') : '-' }}</span></div>
                <div class="info-row"><span class="info-label"><i class="bi bi-shield-check"></i>Status</span><span class="info-value badge {{ $subscription && $subscription->isActive() ? 'bg-success' : 'bg-danger' }}">
                    @if($subscription && $subscription->isActive())
                        Active
                    @else
                        Expired
                    @endif
                </span></div>
            </div>
        </div>
        @endif
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
                <button type="submit" class="btn btn-success">Update</button>
                <button type="button" class="btn btn-secondary" id="cancelEditPanelBtn">Cancel</button>
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
            <i class="bi bi-key me-2"></i>Change Password
          </h5>
          <button type="button" class="btn-close-panel" id="closePanelBtn">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="panel-body">
          @if($errors->any() && session('panel') === 'password')
            <div class="alert alert-danger">
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <form method="POST" action="{{ route('manager.profile.change_password.post') }}">
            @csrf
            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="current_password" name="current_password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="new_password" name="new_password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
              <small class="form-text text-muted">Password must be at least 8 characters long</small>
            </div>
            <div class="mb-3">
              <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <div class="profile-btns">
                <button type="button" class="btn btn-secondary" id="cancelPasswordPanelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update
                </button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
<script src="{{ asset('manager_asset/js/profile.js') }}"></script>
@endsection
