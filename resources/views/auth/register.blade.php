@extends('layout.layout')
@section('welcome_page_title')
Create Your SalesPilot Account
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/register.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <section class="home">
      <div class="form_container">
          <h2>Create SalesPilot Account</h2>
        <form action="{{ route('register') }}" method="post" enctype="multipart/form-data">
        @csrf
      <div class="signup-grid">
        <div class="input_box">
          <input type="text" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required />
          <i class="uil uil-user"></i>
        </div>
        <div class="input_box">
          <input type="text" name="surname" placeholder="Surname" value="{{ old('surname') }}" required />
          <i class="uil uil-user"></i>
        </div>
        <div class="input_box">
          <input type="text" name="other_name" placeholder="Other name(s)" value="{{ old('other_name') }}" />
          <i class="uil uil-user"></i>
        </div>
        <div class="input_box">
          <input type="text" name="business_name" placeholder="Business Name" value="{{ old('business_name') }}" required />
          <i class="uil uil-briefcase"></i>
        </div>
        <div class="input_box">
          <input type="text" name="branch_name" placeholder="Branch Name" value="{{ old('branch_name') }}" required />
          <i class="uil uil-sitemap"></i>
        </div>
        <div class="input_box">
          <label for="businessLogo" class="file-upload-label">
            <span id="logoPlaceholder">Upload business logo</span>
            <input id="businessLogo" name="business_logo" type="file" accept="image/*" class="file-upload-input" />
            <i class="uil uil-image file-icon"></i>
          </label>
        </div>
        <div class="input_box">
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
        <div class="input_box">
          <select id="lgaSelect" name="local_govt" required class="form-select">
            <option value="">Select Local Government Area</option>
          </select>
          <i class="uil uil-map-marker"></i>
        </div>

        <div class="input_box address-full">
          <textarea name="address" placeholder="Address (Number, Street, City)" required rows="3">{{ old('address') }}</textarea>
          <i class="uil uil-location-point"></i>
        </div>

        <div class="input_box">
          <input type="tel" name="phone_number" id="phoneInput" placeholder="Phone number" required pattern="[0-9]{11}" maxlength="11" title="Please enter exactly 11 digits" value="{{ old('phone_number') }}" />
          <i class="uil uil-phone"></i>
        </div>
        <div class="input_box">
          <input type="text" name="referral_code" id="referralcode" placeholder="Agent Referral code(Optional)" maxlength="11" title="Enter agent referral code" value="{{ old('referral_code') }}" />
          <i class="uil uil-user-plus"></i>
        </div>
      </div>
      <div class="input_box email-full">
        <input type="email" name="email" value="{{ old('email', $signup_email ?? '') }}" placeholder="Verified E-mail" required readonly />
        <i class="uil uil-envelope-alt email"></i>
      </div>

      <!-- Hidden role field -->
      <input type="hidden" name="role" value="manager" />

      <div class="password-group">
        <div class="input_box">
          <input type="password" name="password" placeholder="Create password" required />
          <i class="uil uil-lock password"></i>
          <i class="uil uil-eye-slash pw_hide"></i>
        </div>
        <div class="input_box">
          <input type="password" name="password_confirmation" placeholder="Confirm password" required />
          <i class="uil uil-lock password"></i>
          <i class="uil uil-eye-slash pw_hide"></i>
        </div>
      </div>
      <button class="button" type="submit" id="signupSubmitBtn" name="sub">Signup Now</button>
    </form>
  </div>
    </section>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#4CAF50',
        confirmButtonText: 'OK',
        timer: 5000,
        timerProgressBar: true,
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#f44336',
        confirmButtonText: 'OK'
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        html: `
            <ul style="text-align: left; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        `,
        confirmButtonColor: '#f44336',
        confirmButtonText: 'OK',
        width: '500px'
    });
</script>
@endif

 <script src="{{ asset('welcome_asset/js/register_lg.js') }}"></script>
@endsection
