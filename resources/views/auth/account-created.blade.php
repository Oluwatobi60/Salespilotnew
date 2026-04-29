@extends('layout.layout')
@section('welcome_page_title')
Account Created — Check Your Email | {{ app_name() }}
@endsection
@if(session('setup_email'))
    @section('hide_nav_links') 1 @endsection
    @section('brand_bar_step')<span class="sp-brand-step">Step 3 of 3 &mdash; Check Your Email</span>@endsection
@endif
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/account-created.css') }}">

<div class="ac-wrap">
    <div class="ac-card">
        <div class="ac-icon">✉️</div>
        <h2>Account Created!</h2>
        <p>Your subscription is active. Now just check your email to set your password.</p>

        @if($email)
            <div class="ac-email">{{ $email }}</div>
        @endif

        <div class="ac-steps">
            <h4>What to do next</h4>
            <ol>
                <li>Open the email we just sent you</li>
                <li>Click the <strong>"Set My Password"</strong> link</li>
                <li>Choose a secure password</li>
                <li>Log in and start using {{ app_name() }}</li>
            </ol>
        </div>

        <p style="font-size:.85rem;color:#888;">
            Can't find the email? Check your spam/junk folder.<br>
            The link is valid for <strong>48 hours</strong>.
        </p>

        <a href="{{ route('login') }}" class="ac-login-link">
            Already set your password? Go to Login →
        </a>
    </div>
</div>
@endsection
