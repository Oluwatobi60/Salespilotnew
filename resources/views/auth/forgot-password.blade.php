<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - {{ app_name() }}</title>
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
                <h1>Password Recovery</h1>
                <p>Don't worry! Recovering your password is easy. Just enter your email address and we'll send you a reset link.</p>

                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon">🔐</div>
                        <h4>Secure Reset</h4>
                        <p>Bank-level security</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">📧</div>
                        <h4>Email Link</h4>
                        <p>Instant delivery</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">⚡</div>
                        <h4>Quick Process</h4>
                        <p>Reset in minutes</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">✅</div>
                        <h4>Easy Steps</h4>
                        <p>Simple & straightforward</p>
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
                    <h2>Forgot Password?</h2>
                    <p>Enter your email address and we'll send you a password reset link</p>
                </div>

                <div class="form-card">
                    @if (session('status'))
                        <div class="custom-alert alert-success">
                            <i class="uil uil-check-circle"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="input-group">
                            <label for="email">Email Address</label>
                            <div class="input-wrapper">
                                <i class="uil uil-envelope-alt"></i>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    class="@error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    placeholder="your@email.com"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Sending link...">
                            <span class="btn-text">
                                <i class="uil uil-envelope-send"></i> Email Password Reset Link
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
