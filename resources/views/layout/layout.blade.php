<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('welcome_page_title')</title>
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
    <link rel="shortcut icon" href="{{ asset('manager_asset/images/favicon.png') }}" />
  </head>
  <body>

   <!-- Navigation -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <div class="logo">SalesPilot</div>
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
