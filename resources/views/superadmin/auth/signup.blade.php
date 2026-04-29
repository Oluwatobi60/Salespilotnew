<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Sign Up - {{ app_name() }}</title>
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
                <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ app_name() }} Logo" style="max-width: 160px;">
                <h3 class="brand-title mt-3">Superadmin Sign Up</h3>
            </div>

            <div class="card shadow-lg">
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input id="name" type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input id="email" type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone <span class="text-muted">(optional)</span></label>
                            <input id="phone" type="text" name="phone"
                                class="form-control @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-superadmin w-100 py-2 fw-semibold">Create Account</button>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Already have an account?
                            <a href="{{ route('superadmin.login') }}" class="text-decoration-none fw-semibold">Log in</a>
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
