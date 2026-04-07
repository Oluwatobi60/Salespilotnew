<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('superadmin_page_title') - SalesPilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('manager_asset/images/favicon.png') }}" />
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f0f2f5; margin: 0; }

        /* Sidebar */
        .sa-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #5b21b6 0%, #7c3aed 100%);
            width: 240px;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 12px rgba(0,0,0,0.1);
        }
        .sa-sidebar .brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sa-sidebar .brand img { height: 32px; }
        .sa-sidebar nav { flex: 1; padding: 12px 0; }
        .sa-sidebar nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s, color 0.2s;
            border-radius: 0;
        }
        .sa-sidebar nav a:hover,
        .sa-sidebar nav a.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sa-sidebar nav .nav-group-label {
            color: rgba(255,255,255,0.45);
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 14px 20px 4px;
        }
        .sa-sidebar .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        /* Main */
        .sa-main { margin-left: 240px; min-height: 100vh; }

        /* Topbar */
        .sa-topbar {
            background: #fff;
            padding: 14px 28px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .sa-topbar .page-title { font-weight: 700; font-size: 1.1rem; color: #1f1f1f; margin: 0; }
        .sa-topbar .admin-badge {
            background: #ede9fe;
            color: #6d28d9;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Content */
        .sa-content { padding: 28px; }

        /* Cards */
        .sa-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.07);
        }
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        @yield('superadmin_page_styles')
    </style>
</head>
<body>
@php $superadmin = Auth::guard('superadmin')->user(); @endphp

<!-- Sidebar -->
<div class="sa-sidebar">
    <div class="brand">
        <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot">
        <span>Admin Panel</span>
    </div>
    <nav>
        <span class="nav-group-label">Main</span>
        <a href="{{ route('superadmin') }}" class="{{ request()->routeIs('superadmin') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <span class="nav-group-label">Management</span>
        <a href="{{ route('superadmin.customers') }}" class="{{ request()->routeIs('superadmin.customers*') || request()->routeIs('superadmin.users*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Customers
        </a>
        <a href="{{ route('superadmin.brms') }}" class="{{ request()->routeIs('superadmin.brms*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> BRM
        </a>
        <a href="#" class="{{ request()->routeIs('superadmin.subscriptions*') ? 'active' : '' }}">
            <i class="bi bi-patch-check"></i> Subscriptions
        </a>
        <a href="{{ route('superadmin.plans') }}" class="{{ request()->routeIs('superadmin.plans*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Plans
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="text-white-50 small mb-2">{{ $superadmin->name }}</div>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Main -->
<div class="sa-main">
    <!-- Topbar -->
    <div class="sa-topbar">
        <h1 class="page-title">@yield('superadmin_page_title')</h1>
        <div class="d-flex align-items-center gap-3">
            <span class="admin-badge"><i class="bi bi-shield-lock-fill me-1"></i>Superadmin</span>
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-5 text-secondary"></i>
                <span class="fw-semibold text-dark" style="font-size:0.9rem;">{{ $superadmin->name }}</span>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="sa-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('superadmin_layout_content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@yield('superadmin_page_scripts')
</body>
</html>
