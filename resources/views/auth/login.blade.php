<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Login - SalesPilot</title>
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

                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo" class="brand-logo-img">

                </div>
                <h1>Welcome Back!</h1>
                <p>Sign in to manage your inventory, track sales, and grow your business with powerful analytics.</p>

                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon">📊</div>
                        <h4>Real-time Analytics</h4>
                        <p>Track performance instantly</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">🔐</div>
                        <h4>Secure Access</h4>
                        <p>Bank-level security</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">📦</div>
                        <h4>Inventory Control</h4>
                        <p>Manage stock efficiently</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">💼</div>
                        <h4>Business Growth</h4>
                        <p>Scale with confidence</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="form-wrapper">
                <div class="form-logo">
                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo">
                </div>
                <div class="form-header">
                    <h2>Manager Login</h2>
                    <p>Enter your credentials to access your dashboard</p>
                </div>

                <div class="form-card">
                    @if (session('status'))
                        <div class="custom-alert alert-info">
                            <i class="uil uil-info-circle"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
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
                                    autocomplete="username"
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <i class="uil uil-lock-alt"></i>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="@error('password') is-invalid @enderror"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="custom-checkbox">
                                <input type="checkbox" id="remember_me" name="remember">
                                <label for="remember_me">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="forgot-link" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Signing in...">
                            <span class="btn-text">Sign In</span>
                            <span class="btn-spinner"></span>
                        </button>
                    </form>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="staff-login-box">
                        <p>
                            <i class="uil uil-users-alt"></i> Are you a staff member?
                        </p>
                        <a class="btn-outline-custom" href="{{ route('staff.login') }}">
                            <i class="uil uil-arrow-right"></i>
                            Go to Staff Login
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
