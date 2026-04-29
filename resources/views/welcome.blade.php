@extends('layout.layout')
@section('welcome_page_title')
Welcome to SalesPilot
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/style.css') }}">
<link rel="stylesheet" href="{{ asset('welcome_asset/pricing-responsive.css') }}">

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-content">
        <h1>Transform Your Business with Modern Inventory Management</h1>
        <p>SalesPilot is the all-in-one solution for managing inventory, sales, customers, and analytics. Built for modern businesses that demand efficiency and growth.</p>
        <div class="cta">
            <a class="btn btn-primary" href="{{ route('get_started') }}">
                <span>Get Started Free</span>
            </a>
            <a class="btn btn-outline" href="#features">
                <span>Learn More</span>
            </a>
        </div>
    </div>
</section>

<!-- Dynamic Stats Section with Modern Cards -->
<section class="stats-section">
    <div class="stats-container">
        <div class="stats-header">
            <h2>Trusted by Businesses Worldwide</h2>
            <p>Real-time statistics showcasing our impact</p>
        </div>

        <div class="stats-grid">
            <!-- Active Businesses Card -->
            <div class="stat-card">
                <div class="shine"></div>
                <div class="stat-icon-wrapper">
                    <span class="stat-icon">🏢</span>
                </div>
                <div class="stat-value" data-target="{{ $stats['active_businesses'] ?? 0 }}">
                    {{ number_format($stats['active_businesses'] ?? 0) }}+
                </div>
                <div class="stat-label">Active Businesses</div>
                <div class="stat-description">Growing every day</div>
                @if(($stats['active_businesses'] ?? 0) > 0)
                    <div class="stat-trend up">12% this month</div>
                @endif
            </div>

            <!-- Uptime Card -->
            <div class="stat-card">
                <div class="shine"></div>
                <div class="stat-icon-wrapper">
                    <span class="stat-icon">⚡</span>
                </div>
                <div class="stat-value">
                    {{ $stats['uptime'] ?? '99.9' }}%
                </div>
                <div class="stat-label">Uptime Guarantee</div>
                <div class="stat-description">Always available</div>
            </div>

            <!-- Support Card -->
            <div class="stat-card">
                <div class="shine"></div>
                <div class="stat-icon-wrapper">
                    <span class="stat-icon">🎧</span>
                </div>
                <div class="stat-value">
                    {{ $stats['support'] ?? '24/7' }}
                </div>
                <div class="stat-label">Customer Support</div>
                <div class="stat-description">We're here to help</div>
            </div>

            <!-- Transactions Card -->
            <div class="stat-card">
                <div class="shine"></div>
                <div class="stat-icon-wrapper">
                    <span class="stat-icon">💰</span>
                </div>
                <div class="stat-value" data-target="{{ $stats['total_transactions'] ?? 0 }}">
                    @if(($stats['total_transactions'] ?? 0) >= 1000000)
                        {{ number_format(($stats['total_transactions'] ?? 0) / 1000000, 1) }}M+
                    @elseif(($stats['total_transactions'] ?? 0) >= 1000)
                        {{ number_format(($stats['total_transactions'] ?? 0) / 1000, 1) }}K+
                    @else
                        {{ number_format($stats['total_transactions'] ?? 0) }}+
                    @endif
                </div>
                <div class="stat-label">Transactions Processed</div>
                <div class="stat-description">Secure & reliable</div>
                @if(($stats['total_transactions'] ?? 0) > 0)
                    <div class="stat-trend up">18% this quarter</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Count up animation for numbers
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const valueElements = entry.target.querySelectorAll('.stat-value[data-target]');
                        valueElements.forEach(el => {
                            const target = parseInt(el.dataset.target);
                            if (target > 0 && target < 10000) {
                                animateValue(el, 0, target, 2000);
                            }
                        });
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                observer.observe(statsSection);
            }

            function animateValue(element, start, end, duration) {
                const originalText = element.textContent;
                const suffix = originalText.replace(/[0-9,]/g, '');
                let startTimestamp = null;

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const current = Math.floor(progress * (end - start) + start);
                    element.textContent = current.toLocaleString() + suffix;
                    element.classList.add('animate');

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };

                window.requestAnimationFrame(step);
            }

            // Add pulse effect on hover
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.animation = 'none';
                    setTimeout(() => {
                        this.style.animation = '';
                    }, 10);
                });
            });
        });
    </script>
</section>


        <!-- Features Section -->
		<section class="container" id="features">
			<div class="section-header">
				<h2>Powerful Features for Growing Businesses</h2>
				<p>Everything you need to manage and grow your retail business efficiently</p>
			</div>
			<div class="features">
				<div class="feature">
					<div class="feature-icon">📦</div>
					<h3>Inventory Management</h3>
					<p>Track stock levels in real-time, manage variants, SKUs, barcodes, and receive low-stock alerts. Never run out of your best-selling products again.</p>
				</div>

				<div class="feature">
					<div class="feature-icon">👥</div>
					<h3>Customer Relationship Management</h3>
					<p>Build lasting relationships with integrated CRM tools. Store customer data, track purchase history, and create targeted marketing campaigns.</p>
				</div>
				<div class="feature">
					<div class="feature-icon">📊</div>
					<h3>Advanced Analytics</h3>
					<p>Make data-driven decisions with comprehensive reports on sales, inventory, staff performance, and customer behavior. Visualize trends with beautiful charts.</p>
				</div>
				<div class="feature">
					<div class="feature-icon">🔄</div>
					<h3>Multi-Location Support</h3>
					<p>Manage multiple stores from a single dashboard. Transfer inventory between locations and get unified reports across all your business outlets.</p>
				</div>
				<div class="feature">
					<div class="feature-icon">🔐</div>
					<h3>Secure & Reliable</h3>
					<p>Bank-level security with encrypted data, automated backups, and role-based access control to keep your business information safe and secure.</p>
				</div>
				<div class="feature">
					<div class="feature-icon">💳</div>
					<h3> Point of Sale (POS)<sub style="color: red; font-size: 0.6em;">(In view)</sub></h3>
					<p>Lightning-fast checkout experience with support for multiple payment methods, receipt printing, and seamless cart management for busy retail environments.</p>
				</div>
			</div>
		</section>


<!-- Pricing Section -->

	<section class="pricing py-5" id="pricing">
	 <div class="container pricing-container">
		<div class="section-header text-center mb-5">
			<h2 class="pricing-title">Choose Your Perfect Plan</h2>
			<p class="pricing-subtitle">Select a plan that fits your business needs</p>
        </div>

        <!-- Duration Selector -->
		<div class="duration-selector-wrapper">
			<div class="duration-selector">
				<button type="button" class="duration-btn active" data-months="1">
                    1 Month
                </button>
				<button type="button" class="duration-btn" data-months="3">
                    3 Months
                    <span>Save 5%</span>
                </button>
				<button type="button" class="duration-btn" data-months="6">
                    6 Months
                    <span>Save 10%</span>
                </button>
				<button type="button" class="duration-btn" data-months="12">
                    1 Year
                    <span>Save 15%</span>
                </button>
            </div>
        </div>

		<div class="pricing-grid mb-4">
            <!-- Free Plan -->
			<div class="pricing-card">
                <h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Free</h3>
                <div class="price" style="margin: 20px 0;">
                    <span style="font-size: 48px; font-weight: bold; color: #4CAF50;">₦0</span>
                    <span style="font-size: 18px; color: #666;">/7-Days</span>
                    <span style="font-size: 18px; color: #666;">Test all features risk-free</span>
                </div>
                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 1 Manager/Administrator Account</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 1 Staff Account</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Basic Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Sales Tracking</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Email Support</li>
                </ul>
                {{--  <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="free">
                    <input type="hidden" name="duration" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Get Started
                    </button>
                </form>  --}}
            </div>



              <!-- Basic Plan -->
			<div class="pricing-card popular">
				<div class="popular-badge">
                    Most Popular
                </div>
				<h3 class="pricing-plan-title">Basic</h3>
				<div class="price" data-monthly-price="5000">
					<div class="mb-2">
						<span class="calculated-price">₦14,250</span>
					</div>
					<div class="original-price">₦15,000</div>
					<div class="duration-text">for 3 months</div>
                </div>
				<ul class="pricing-features">
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 1 Manager/Administrator Account</li>
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 2 Staff Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Advanced Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Sales & Purchase Tracking</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Basic Reports & Analytics</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Priority Email Support</li>
                </ul>
               {{--   <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="basic">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>  --}}
            </div>


            <!-- Standard Plan -->
			<div class="pricing-card popular">
				<div class="popular-badge">
                    Most Popular
                </div>
				<h3 class="pricing-plan-title">Standard</h3>
				<div class="price" data-monthly-price="10000">
					<div class="mb-2">
						<span class="calculated-price">₦28,500</span>
					</div>
					<div class="original-price">₦30,000</div>
					<div class="duration-text">for 3 months</div>
                </div>
				<ul class="pricing-features">
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 2 Manager/Administrator Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Up to 4 Staff Accounts</li>
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Allows 2 branches</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Advanced Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Sales & Purchase Tracking</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Basic Reports & Analytics</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Priority Email Support</li>
                </ul>
               {{--   <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="standard">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>  --}}
            </div>

            <!-- Premium Plan -->
			<div class="pricing-card">
                <h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Premium</h3>
                <div class="price" style="margin: 20px 0;" data-monthly-price="20000">
                    <div style="margin-bottom: 10px;">
                        <span class="calculated-price" style="font-size: 48px; font-weight: bold; color: #4CAF50;">₦57,000</span>
                    </div>
                    <div style="font-size: 14px; color: #999; text-decoration: line-through;" class="original-price">₦60,000</div>
                    <div style="font-size: 16px; color: #4CAF50; font-weight: 600; margin-top: 5px;" class="duration-text">for 3 months</div>
                </div>
                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 3 Manager/Administrator Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Unlimited Staff Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Full Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Advanced Reports & Analytics</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Multi-branch Support</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 24/7 Priority Support</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Custom Integrations</li>
                </ul>
                {{--  <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="premium">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>  --}}
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <p style="color: #666; margin-bottom: 15px;">Need help choosing? <a href="#" style="color: #4CAF50; text-decoration: none; font-weight: 600;">Contact our sales team</a></p>
        </div>
    </div>
</section>


    	<!-- About Section -->
		<section class="container" id="about">
			<div class="about-content">
				<div class="about-text">
					<h2>About SalesPilot</h2>
					<p>SalesPilot was founded with a simple mission: to empower small and medium-sized businesses with enterprise-grade inventory and point-of-sale solutions that are both powerful and easy to use.</p>
					<p>We understand the challenges of running a retail business. That's why we've built a platform that combines sophisticated inventory management, seamless POS operations, and insightful analytics in one intuitive interface.</p>
					<p>Our team is dedicated to helping businesses grow through technology. With continuous updates, responsive support, and a commitment to your success, SalesPilot is more than just software—it's your business partner.</p>
					<a href="{{ route('get_started') }}" class="btn btn-primary btn-large">Start Your Free Trial</a>
				</div>
				<div class="about-image">
					📈
				</div>
			</div>
		</section>

        	<!-- Contact Section -->
		<section class="contact" id="contact">
			<div class="container">
				<div class="section-header">
					<h2>Get In Touch</h2>
					<p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
				</div>
				<div class="contact-grid">
					<div class="contact-info">
						<div class="contact-item">
							<div class="contact-icon">📧</div>
							<div>
								<h3>Email Us</h3>
								<a href="mailto:info.salespilots@gmail.com">info.salespilots@gmail.com</a><br>
								<a href="mailto:support@salespilot.com">support@salespilot.com</a>
							</div>
						</div>
						<div class="contact-item">
							<div class="contact-icon">📞</div>
							<div>
								<h3>Call Us</h3>
								<p>+234 800 123 4567</p>
								<p>Mon-Fri, 8am-6pm WAT</p>
							</div>
						</div>
						<div class="contact-item">
							<div class="contact-icon">📍</div>
							<div>
								<h3>Visit Us</h3>
								<p>123 Business District<br>Lagos, Nigeria</p>
							</div>
						</div>
						<div class="contact-item">
							<div class="contact-icon">🌐</div>
							<div>
								<h3>Follow Us</h3>
									<div class="social-links">
									<a href="#" title="Facebook">FB</a>
									<a href="#" title="Twitter">X</a>
									<a href="#" title="LinkedIn">in</a>
									<a href="#" title="Instagram">IG</a>
									</div>
							</div>
						</div>
					</div>
					<div class="contact-form">
						<form action="#" method="POST">
							<div class="form-group">
								<label for="name">Full Name</label>
								<input type="text" id="name" name="name" required>
							</div>
							<div class="form-group">
								<label for="email">Email Address</label>
								<input type="email" id="email" name="email" required>
							</div>
							<div class="form-group">
								<label for="subject">Subject</label>
								<input type="text" id="subject" name="subject" required>
							</div>
							<div class="form-group">
								<label for="message">Message</label>
								<textarea id="message" name="message" required></textarea>
							</div>
							<button type="submit" class="btn submit-btn">Send Message</button>
						</form>
					</div>
				</div>
			</div>
		</section>


        	<!-- Footer -->
		<footer class="footer">
			<div class="footer-content">
				<div class="footer-section">
					<h3>SalesPilot</h3>
					<p>Empowering businesses with modern inventory and POS solutions.</p>
					<div class="social-links">
						<a href="#">f</a>
						<a href="#">𝕏</a>
						<a href="#">in</a>
						<a href="#">📷</a>
					</div>
				</div>
				<div class="footer-section">
					<h3>Product</h3>
					<ul class="footer-links">
						<li><a href="#features">Features</a></li>
						<li><a href="#pricing">Pricing</a></li>
						<li><a href="plans.php">View Plans</a></li>
						<li><a href="sign_up.php">Sign Up</a></li>
					</ul>
				</div>
				<div class="footer-section">
					<h3>Company</h3>
					<ul class="footer-links">
						<li><a href="#about">About Us</a></li>
						<li><a href="#contact">Contact</a></li>
						<li><a href="#">Careers</a></li>
						<li><a href="#">Blog</a></li>
					</ul>
				</div>
				<div class="footer-section">
					<h3>Support</h3>
					<ul class="footer-links">
						<li><a href="#">Help Center</a></li>
						<li><a href="#">Documentation</a></li>
						<li><a href="#">Privacy Policy</a></li>
						<li><a href="#">Terms of Service</a></li>
					</ul>
				</div>
			</div>
			<div class="footer-bottom">
				<p>&copy; 2026 SalesPilot. All rights reserved. Built with ❤️ for businesses everywhere.</p>
			</div>
		</footer>

        <script src="{{ asset('manager_asset/js/pricing_plan.js') }}"></script>
@endsection
