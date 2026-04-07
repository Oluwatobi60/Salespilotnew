<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Login - SalesPilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .brand-title {
            font-family: 'Montserrat', Arial, sans-serif;
            font-size: 2rem;
            color: #6f42c1;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .card { border: none; border-radius: 14px; }
        .btn-superadmin { background: #6f42c1; color: #fff; border: none; }
        .btn-superadmin:hover { background: #5a32a3; color: #fff; }
    </style>
</head>
<body>
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-4">
                <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo" style="max-width: 160px;">
                <h3 class="brand-title mt-3">Superadmin Login</h3>
            </div>

            <div class="card shadow-lg">
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-info">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required autofocus autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-superadmin w-100 py-2 fw-semibold">Log In</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Don't have an account?
                            <a href="{{ route('superadmin.signup') }}" class="text-decoration-none fw-semibold">Sign up</a>
                        </small>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
