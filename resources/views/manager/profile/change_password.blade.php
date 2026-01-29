@extends('manager.layouts.layout')
@section('manager_page_title')
Change Password
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/profile_style.css') }}">
<div class="password-panel" id="passwordPanel">
  <div class="panel-overlay" id="panelOverlay"></div>
  <div class="panel-content">
    <div class="panel-header">
      <h5 class="panel-title">
        <i class="bi bi-key me-2"></i>Change Password
      </h5>
      <a href="{{ route('manager.profile.show') }}" class="btn-close-panel" id="closePanelBtn">
        <i class="bi bi-x-lg"></i>
      </a>
    </div>
    <div class="panel-body">
      @if($errors->any())
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
        <div class="panel-footer">
          <a href="{{ route('manager.profile.show') }}" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i>Update Password
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="{{ asset('manager_asset/js/profile.js') }}"></script>
@endsection
