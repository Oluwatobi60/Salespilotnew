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
        *, *::before, *::after { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { background: #f0f2f5; margin: 0; }

        /* ── Sidebar ─────────────────────────────── */
        .sa-sidebar {
            width: 240px;
            min-height: 100vh;
            background: linear-gradient(180deg, #5b21b6 0%, #7c3aed 100%);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            box-shadow: 4px 0 12px rgba(0,0,0,0.1);
            transition: transform 0.28s cubic-bezier(.4,0,.2,1);
        }
        .sa-sidebar .brand {
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.15);
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .sa-sidebar .brand img { height: 30px; }
        .sa-sidebar nav { flex: 1; padding: 10px 0; overflow-y: auto; }
        .sa-sidebar nav a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            font-size: 0.88rem;
            font-weight: 500;
            transition: background 0.18s, color 0.18s;
        }
        .sa-sidebar nav a i { font-size: 1rem; flex-shrink: 0; }
        .sa-sidebar nav a:hover,
        .sa-sidebar nav a.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sa-sidebar nav .nav-group-label {
            color: rgba(255,255,255,0.45);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 12px 20px 3px;
            display: block;
        }
        .sa-sidebar .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.15);
            flex-shrink: 0;
        }

        /* ── Mobile overlay ──────────────────────── */
        .sa-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1049;
            backdrop-filter: blur(2px);
        }
        .sa-overlay.show { display: block; }

        /* ── Hamburger button ────────────────────── */
        .sa-burger {
            display: none;
            background: none;
            border: none;
            padding: 5px 7px;
            color: #374151;
            font-size: 1.35rem;
            cursor: pointer;
            line-height: 1;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .sa-burger:hover { background: #f3f4f6; }

        /* ── Main area ───────────────────────────── */
        .sa-main {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ──────────────────────────────── */
        .sa-topbar {
            background: #fff;
            padding: 12px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 10px;
            min-height: 56px;
        }
        .sa-topbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }
        .sa-topbar .page-title {
            font-weight: 700;
            font-size: 1.05rem;
            color: #1f1f1f;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sa-topbar .admin-badge {
            background: #ede9fe;
            color: #6d28d9;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        /* ── Page content ────────────────────────── */
        .sa-content { padding: 24px; flex: 1; }

        /* ── Cards ───────────────────────────────── */
        .sa-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 8px rgba(0,0,0,0.07);
        }
        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        /* ── Responsive breakpoints ──────────────── */
        /* Tablet / small laptop  ≤ 991px */
        @media (max-width: 991.98px) {
            .sa-sidebar { transform: translateX(-100%); }
            .sa-sidebar.open { transform: translateX(0); }
            .sa-main { margin-left: 0; }
            .sa-burger { display: block; }
            .sa-topbar .admin-badge { display: none; }
        }

        /* Mobile ≤ 575px */
        @media (max-width: 575.98px) {
            .sa-content { padding: 12px; }
            .sa-topbar { padding: 10px 12px; }
            .sa-card { padding: 14px; }
            .sa-topbar .page-title { font-size: 0.9rem; }
            .stat-icon { width: 38px; height: 38px; font-size: 1rem; }
        }

        @yield('superadmin_page_styles')
    </style>
</head>
<body>
@php $superadmin = Auth::guard('superadmin')->user(); @endphp

<!-- Mobile overlay -->
<div class="sa-overlay" id="saOverlay"></div>

<!-- Sidebar -->
<div class="sa-sidebar" id="saSidebar">
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
        <div class="text-white-50 small mb-2 text-truncate">{{ $superadmin->name }}</div>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Main wrapper -->
<div class="sa-main">

    <!-- Topbar -->
    <div class="sa-topbar">
        <div class="sa-topbar-left">
            <button class="sa-burger" id="saBurger" aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title">@yield('superadmin_page_title')</h1>
        </div>
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <span class="admin-badge"><i class="bi bi-shield-lock-fill me-1"></i>Superadmin</span>
            <i class="bi bi-person-circle fs-5 text-secondary"></i>
            <span class="fw-semibold text-dark d-none d-md-inline" style="font-size:0.88rem;">{{ $superadmin->name }}</span>
        </div>
    </div>

    <!-- Page content -->
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
<script>
(function () {
    var sidebar = document.getElementById('saSidebar');
    var overlay = document.getElementById('saOverlay');
    var burger  = document.getElementById('saBurger');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    burger.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Auto-close when a nav link is tapped on mobile
    sidebar.querySelectorAll('nav a').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.innerWidth < 992) closeSidebar();
        });
    });
}());
</script>
@yield('superadmin_page_scripts')
</body>
</html>