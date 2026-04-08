@extends('layout.layout')
@section('welcome_page_title')
Set Your Password | SalesPilot
@endsection
@section('hide_nav_links') 1 @endsection
@section('brand_bar_step')<span class="sp-brand-step">Final Step &mdash; Set Your Password</span>@endsection
@section('welcome_page_content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="{{ asset('manager_asset/css/set-password.css') }}">

<div class="sp-wrap">
    <div class="sp-card">
        <div class="sp-icon">🔐</div>
        <h2>Create Your Password</h2>
        <p class="subtitle">Choose a strong password to secure your SalesPilot account.</p>

        <form method="POST" action="{{ route('password.setup.store', $token) }}">
            @csrf

            <label class="sp-label" for="password">Password</label>
            <div class="sp-input-wrap">
                <input type="password" id="password" name="password"
                       placeholder="Enter your password" required autocomplete="new-password">
                <span class="sp-toggle" onclick="toggleVisibility('password', this)">👁</span>
            </div>
            <p class="sp-hint">Minimum 8 characters — use letters, numbers and symbols.</p>

            <label class="sp-label" for="password_confirmation">Confirm Password</label>
            <div class="sp-input-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       placeholder="Repeat your password" required autocomplete="new-password">
                <span class="sp-toggle" onclick="toggleVisibility('password_confirmation', this)">👁</span>
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

<script>
function toggleVisibility(fieldId, icon) {
    const input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = '🙈';
    } else {
        input.type = 'password';
        icon.textContent = '👁';
    }
}
</script>
@endsection
