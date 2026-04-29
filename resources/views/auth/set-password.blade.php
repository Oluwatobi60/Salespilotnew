@extends('layout.layout')
@section('welcome_page_title')
Set Your Password | {{ app_name() }}
@endsection
@section('hide_nav_links') 1 @endsection
@section('brand_bar_step')<span class="sp-brand-step">Final Step &mdash; Set Your Password</span>@endsection
@section('welcome_page_content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('manager_asset/css/set-password.css') }}">
<link rel="stylesheet" href="{{ asset('manager_asset/css/password-validation.css') }}">

<div class="sp-wrap">
    <div class="sp-card">
        <div class="sp-icon">🔐</div>
        <h2>Create Your Password</h2>
        <p class="subtitle">Choose a strong password to secure your {{ app_name() }} account.</p>

        <form id="setPasswordForm" method="POST" action="{{ route('password.setup.store', $token) }}">
            @csrf

            <label class="sp-label" for="password">Password</label>
            <div class="sp-input-wrap">
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required autocomplete="new-password">
            </div>

            <label class="sp-label" for="password_confirmation">Confirm Password</label>
            <div class="sp-input-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Repeat your password" required autocomplete="new-password">
            </div>

            <button type="submit" class="sp-btn">Set Password &amp; Go to Login</button>
        </form>

        <a href="{{ route('login') }}" class="sp-back">Back to Login</a>
    </div>
</div>

@if(session('error'))
<script>
    Swal.fire({ icon:'error', title:'Error', text:'{{ session('error') }}', confirmButtonColor:'#7c3aed' });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        html: `<ul style="text-align:left;padding-left:20px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>`,
        confirmButtonColor: '#7c3aed'
    });
</script>
@endif

<!-- Password Validator Component -->
<script src="{{ asset('manager_asset/js/password-validator.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize password validator for set-password form
        const validator = new PasswordValidator({
            passwordSelector: '#password',
            confirmSelector: '#password_confirmation',
            formSelector: '#setPasswordForm',
            minLength: 8,
            showToggle: true,
            requiredConfirm: true
        });
    });
</script>
@endsection
