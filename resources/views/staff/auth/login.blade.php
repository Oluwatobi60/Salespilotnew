<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - SalesPilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('welcome_asset/staff_login.css') }}">
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
                <h1>Staff Portal</h1>
                <p>Access your work dashboard, manage daily tasks, and help customers with seamless service.</p>

                <div class="features-grid">
                    <div class="feature-box">
                        <div class="feature-icon">🛒</div>
                        <h4>Quick Sales</h4>
                        <p>Process transactions fast</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">📋</div>
                        <h4>Task Management</h4>
                        <p>Track daily activities</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">👥</div>
                        <h4>Customer Service</h4>
                        <p>Support excellence</p>
                    </div>
                    <div class="feature-box">
                        <div class="feature-icon">📊</div>
                        <h4>Performance Tracking</h4>
                        <p>Monitor your metrics</p>
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
                    <h2>Staff Login</h2>
                    <p>Enter your credentials to access your work dashboard</p>
                </div>

                <div class="form-card">
                    @if (session('status'))
                        <div class="custom-alert alert-info">
                            <i class="uil uil-info-circle"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('staff.login.submit') }}">
                        @csrf

                        <div class="input-group">
                            <label for="login">Email or Staff ID</label>
                            <div class="input-wrapper">
                                <i class="uil uil-user"></i>
                                <input 
                                    id="login" 
                                    type="text" 
                                    name="login" 
                                    class="@error('login') is-invalid @enderror" 
                                    value="{{ old('login') }}" 
                                    placeholder="Enter your email or staff ID"
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                >
                                @error('login')
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
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>
</body>
</html>
