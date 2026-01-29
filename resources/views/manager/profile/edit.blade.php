@extends('manager.layouts.layout')
@section('manager_page_title')
Edit Profile
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/profile_style.css') }}">
<div class="edit-panel" id="editPanel">
  <div class="panel-overlay" id="editPanelOverlay"></div>
  <div class="panel-content">
    <div class="panel-header">
      <h5 class="panel-title">
        <i class="bi bi-pencil-square me-2"></i>Edit Profile
      </h5>
      <a href="{{ route('manager.profile.show') }}" class="btn-close-panel" id="closeEditPanelBtn">
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
          <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $manager->email) }}" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $manager->phone_number) }}">
        </div>
        <div class="mb-3">
          <label for="business_name" class="form-label">Business Name</label>
          <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $manager->business_name) }}" required>
        </div>
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $manager->address) }}">
        </div>
        <button type="submit" class="btn btn-success">Update Profile</button>
        <a href="{{ route('manager.profile.show') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
</div>
<script src="{{ asset('manager_asset/js/profile.js') }}"></script>
@endsection
