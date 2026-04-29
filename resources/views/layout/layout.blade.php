<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('welcome_page_title', app_name())</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
     <link rel="stylesheet" href="{{ asset('welcome_asset/style.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- endinject -->
    <link rel="shortcut icon" href="{{ app_favicon() }}" />
  </head>
  <body>

   <!-- Navigation -->
@if(View::hasSection('hide_nav_links'))
<style>
    .sp-brand-bar {
        background: linear-gradient(135deg, #4c1d95 0%, #6d28d9 50%, #7c3aed 100%);
        padding: 0;
        box-shadow: 0 2px 20px rgba(109,40,217,.25);
        position: sticky;
        top: 0;
        z-index: 1000;
    }
    .sp-brand-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .sp-brand-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .sp-brand-icon {
        width: 42px;
        height: 42px;
        background: rgba(255,255,255,.18);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.25);
    }
    .sp-brand-text {
        display: flex;
        flex-direction: column;
        line-height: 1;
    }
    .sp-brand-name {
        font-size: 1.45rem;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.4px;
    }
    .sp-brand-name span {
        color: #c4b5fd;
    }
    .sp-brand-tagline {
        font-size: .7rem;
        color: rgba(255,255,255,.65);
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-top: 2px;
    }
    .sp-brand-step {
        position: absolute;
        right: 24px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: .75rem;
        color: rgba(255,255,255,.85);
        font-weight: 500;
        backdrop-filter: blur(4px);
    }
    @media(max-width:480px) { .sp-brand-step { display: none; } }
</style>
<div class="sp-brand-bar">
    <div class="sp-brand-inner">
        <a href="{{ route('get_started') }}" class="sp-brand-logo">
            <div class="sp-brand-icon">📊</div>
            <div class="sp-brand-text">
                <div class="sp-brand-name">{{ app_name() }}</div>
                <div class="sp-brand-tagline">{{ setting('app_tagline', 'Business Management Suite') }}</div>
            </div>
        </a>
        @yield('brand_bar_step')
    </div>
</div>
@else
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <div class="logo">{{ app_name() }}</div>
        <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
        <ul class="nav-links" id="navLinks">
            <li><a href="#home">Home</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#pricing">Pricing</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>

            <!-- Auth Links from header -->
            @if (Route::has('login'))
                <li class="auth-links">
                    <a href="{{ route('login') }}">Log in</a>

                    @if (Route::has('get_started'))
                        <a href="{{ route('get_started') }}">Get Started</a>
                    @endif
                </li>
            @endif
        </ul>
    </div>
</nav>
@endif

    @yield('welcome_page_content')

    <script>
        // Mobile menu toggle
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const navbar = document.getElementById('navbar');
            const navLinks = document.getElementById('navLinks');
            const mobileBtn = document.querySelector('.mobile-menu-btn');

            if (!navbar.contains(event.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth'
                    });
                    // Close mobile menu after clicking
                    const navLinks = document.getElementById('navLinks');
                    navLinks.classList.remove('active');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

  </body>
</html>
