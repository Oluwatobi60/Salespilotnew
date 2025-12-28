<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <style>
        .staff-login-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2.2rem;
            color: #0d6efd;
            text-shadow: 1px 2px 8px rgba(13,110,253,0.12);
            letter-spacing: 1px;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-5">
                <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo" class="mb-4" style="max-width: 180px; display: block; margin-left: auto; margin-right: auto;">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                         <h3 class="mb-4 text-center staff-login-title">Staff Login</h3>
                        @if (session('status'))
                            <div class="alert alert-info">{{ session('status') }}</div>
                        @endif
                        <form method="POST" action="{{ route('staff.login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                <label class="form-check-label" for="remember_me">Remember me</label>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                @if (Route::has('password.request'))
                                    <a class="text-decoration-none" href="{{ route('password.request') }}">Forgot your password?</a>
                                @endif
                                <button type="submit" class="btn btn-primary">Log in</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
