<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ app_name() }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('welcome_asset/login.css') }}">
    <link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="login-branding">
                <div class="brand-logo-wrapper">
                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ app_name() }} Logo" class="brand-logo-img">
                </div>
                <h1>Create New Password</h1>
                <p>You're almost there! Choose a strong password to secure your account and regain access to your dashboard.</p>

                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon">🔐</div>
                        <h4>Strong Security</h4>
                        <p>Keep your account safe</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">🔑</div>
                        <h4>New Password</h4>
                        <p>Choose wisely</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">✅</div>
                        <h4>Instant Access</h4>
                        <p>Login immediately</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">🛡️</div>
                        <h4>Protected</h4>
                        <p>Encrypted & secure</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="form-wrapper">
                <div class="form-logo">
                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ app_name() }} Logo">
                </div>
                <div class="form-header">
                    <h2>Reset Password</h2>
                    <p>Enter your new password below</p>
                </div>

                <div class="form-card">
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <div class="input-wrapper">
                                <i class="uil uil-envelope-alt"></i>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    class="@error('email') is-invalid @enderror"
                                    value="{{ old('email', $request->email) }}"
                                    placeholder="your@email.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="input-group">
                            <label for="password">New Password</label>
                            <div class="input-wrapper">
                                <i class="uil uil-lock-alt"></i>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="@error('password') is-invalid @enderror"
                                    placeholder="Enter new password"
                                    required
                                    autocomplete="new-password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <div class="input-wrapper">
                                <i class="uil uil-lock-alt"></i>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="@error('password_confirmation') is-invalid @enderror"
                                    placeholder="Confirm new password"
                                    required
                                    autocomplete="new-password"
                                >
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Resetting password...">
                            <span class="btn-text">
                                <i class="uil uil-check"></i> Reset Password
                            </span>
                            <span class="btn-spinner"></span>
                        </button>
                    </form>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="staff-login-box">
                        <p>
                            <i class="uil uil-arrow-left"></i> Remember your password?
                        </p>
                        <a class="btn-outline-custom" href="{{ route('login') }}">
                            Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>
</body>
</html>
