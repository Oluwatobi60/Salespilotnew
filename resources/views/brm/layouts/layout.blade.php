<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('brms_page_title') - SalesPilot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('manager_asset/images/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('brm_asset/css/brm_layout.css') }}">
</head>
<body>
@php $brm = Auth::guard('brms')->user(); @endphp

<!-- Mobile overlay -->
<div class="sa-overlay" id="saOverlay"></div>

<!-- Fixed Logo Container - Like Manager Layout -->
<div class="fixed-logo-container">
  <div class="logo-hamburger-wrapper">
    <button id="sidebarToggle" class="navbar-toggler navbar-toggler align-self-center" type="button" aria-label="Toggle sidebar" title="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <a class="navbar-brand brand-logo" href="{{ route('brm.dashboard') }}">
      <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot">
    </a>
  </div>
</div>

<!-- Sidebar Navigation - Positioned below fixed container -->
<nav class="sidebar sidebar-offcanvas" id="saSidebar">
  <ul class="nav">
    <li class="nav-item">
      <a href="{{ route('brm.dashboard') }}" class="nav-link {{ request()->routeIs('brm.dashboard') ? 'active' : '' }}">
        <i class="menu-icon bi bi-house-fill"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
{{--
    <li class="nav-item nav-category">Menu</li>  --}}

    <li class="nav-item">
      <a href="{{ route('brm.customers') }}" class="nav-link {{ request()->routeIs('brm.customers*') || request()->routeIs('brm.users*') ? 'active' : '' }}">
        <i class="menu-icon bi bi-people-fill"></i>
        <span class="menu-title">My Customers</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#commissions-menu" aria-expanded="false" aria-controls="commissions-menu" role="button">
        <i class="menu-icon bi bi-cash-stack"></i>
        <span class="menu-title">Commissions</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="commissions-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link {{ request()->routeIs('brm.commissions') ? 'active' : '' }}" href="{{ route('brm.commissions') }}">My Commissions</a></li>
          <li class="nav-item"> <a class="nav-link {{ request()->routeIs('brm.commissions.history') ? 'active' : '' }}" href="{{ route('brm.commissions.history') }}">History</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('brm.performance') }}" class="nav-link {{ request()->routeIs('brm.performance*') ? 'active' : '' }}">
        <i class="menu-icon bi bi-graph-up"></i>
        <span class="menu-title">Performance</span>
      </a>
    </li>

    <li class="nav-item dropdown user-dropdown">
      <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer; display: flex; align-items: center; padding: 15px 20px; justify-content: center;">
        <img class="img-xs rounded-circle" src="{{ asset('manager_asset/images/faces/face1.jpg') }}" alt="Profile image" style="width: 40px; height: 40px; object-fit: cover;">
      </a>
      <div class="dropdown-menu dropdown-menu-center navbar-dropdown" aria-labelledby="UserDropdown" style="min-width: 250px;">
        <div class="dropdown-header text-center" style="padding: 20px;">
          <img class="img-md rounded-circle" src="{{ asset('manager_asset/images/faces/face1.jpg') }}" alt="Profile image" style="width: 80px; height: 80px; object-fit: cover;">
          <p class="mb-1 mt-3 fw-semibold">{{ $brm->name }}</p>
          <p class="fw-light text-muted mb-0">{{ $brm->email }}</p>
        </div>
        <a class="dropdown-item" href="#" style="padding: 10px 20px;"><i class="dropdown-item-icon bi bi-person text-primary me-2"></i> Edit Profile</a>
        <form method="POST" action="{{ route('brm.logout') }}" style="display: inline;">
          @csrf
          <button type="submit" class="dropdown-item" style="padding: 10px 20px; border: none; background: none; cursor: pointer; text-align: left; width: 100%;"><i class="dropdown-item-icon bi bi-box-arrow-right text-primary me-2"></i>Sign Out</button>
        </form>
      </div>
    </li>
  </ul>
</nav>

<!-- Main wrapper -->
<div class="page-body-wrapper">
    <!-- Page content -->
    <div class="main-panel">
      <div class="content-wrapper">
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

        @yield('brms_page_content')
     </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var sidebar = document.getElementById('saSidebar');
    var overlay = document.getElementById('saOverlay');
    var burger  = document.getElementById('sidebarToggle');

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

    if (burger) {
        burger.addEventListener('click', function () {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Auto-close when a nav link is clicked on mobile
    sidebar.querySelectorAll('nav a').forEach(function (a) {
        a.addEventListener('click', function () {
            if (window.innerWidth < 992) closeSidebar();
        });
    });
}());
</script>
@yield('brms_page_scripts')
</body>
</html>
