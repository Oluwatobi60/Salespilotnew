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
    <link rel="stylesheet" href="{{ asset('superadmin_asset/css/superadmin_layout.css') }}">
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
        <a href="{{ route('superadmin.subscriptions') }}" class="{{ request()->routeIs('superadmin.subscriptions*') ? 'active' : '' }}">
            <i class="bi bi-patch-check"></i> Subscriptions
        </a>
        <a href="{{ route('superadmin.plans') }}" class="{{ request()->routeIs('superadmin.plans*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i> Plans
        </a>
        <a href="{{ route('superadmin.revenue') }}" class="{{ request()->routeIs('superadmin.revenue*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i> Revenue
        </a>
        <a href="{{ route('superadmin.commissions') }}" class="{{ request()->routeIs('superadmin.commissions*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Commissions
        </a>
        <a href="{{ route('superadmin.withdrawals') }}" class="{{ request()->routeIs('superadmin.withdrawals*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> Withdrawals
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