@extends('layout.layout')
@section('welcome_page_title')
Welcome to SalesPilot
@endsection
@section('welcome_page_content')

<!-- Hero Section -->
		<section class="hero" id="home">
			<div class="hero-content">
				<h1>Transform Your Business with Modern Inventory Management</h1>
				<p>SalesPilot is the all-in-one solution for managing inventory, sales, customers, and analytics. Built for modern businesses that demand efficiency and growth.</p>
				<div class="cta">
					<a class="btn btn-primary" href="{{ route('get_started') }}">Get Started Free</a>
					<a class="btn btn-outline" href="#features">Learn More</a>
				</div>
			</div>
		</section>

	<!-- Stats Section -->
		<section class="stats">
			<div class="stats-grid">
				<div class="stat">
					<h3>5,000+</h3>
					<p>Active Businesses</p>
				</div>
				<div class="stat">
					<h3>99.9%</h3>
					<p>Uptime Guarantee</p>
				</div>
				<div class="stat">
					<h3>24/7</h3>
					<p>Customer Support</p>
				</div>
				<div class="stat">
					<h3>50M+</h3>
					<p>Transactions Processed</p>
				</div>
			</div>
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

    <section class="pricing" id="pricing">
			<div class="container">
				<div class="section-header">
					<h2>Simple, Transparent Pricing</h2>
					<p>Choose the plan that's right for your business. No hidden fees.</p>
				</div>
				<div class="pricing-cards">
					<div class="pricing-card">
						<h3>Basic</h3>
						<div class="price">₦5,000<span>/month</span></div>
						<ul class="features-list">
							<li>Up to 500 products</li>
							<li>2 Users account</li>
							<li>Basic reports</li>
							<li>Email support</li>
							<li>Single Manager Support</li>
						</ul>
						<a href="sign_up.php" class="btn btn-outline" style="width: 100%; text-align: center;">Choose Plan</a>
					</div>
					<div class="pricing-card featured">
						<h3>Standard</h3>
						<div class="price">₦10,000<span>/month</span></div>
						<ul class="features-list">
							<li>Unlimited products</li>
							<li>4 User accounts</li>
							<li>Priority support</li>
							<li>Multi-location(2-branches)</li>
							<li>Custom receipts</li>
						</ul>
						<a href="sign_up.php" class="btn btn-primary" style="width: 100%; text-align: center;">Choose Plan</a>
					</div>
					<div class="pricing-card">
						<h3>Premium</h3>
						<div class="price">₦20,000<span>/month</span></div>
						<ul class="features-list">
							<li>Everything in Standard</li>
							<li>Up to 10 users</li>
							<li>Multi-location(3-branches)</li>
							<li>Dedicated support</li>
							<li>Supports up to 3 manager accounts</li>
							<li>Advanced analytics & Reporting</li>
						</ul>
						<a href="sign_up.php" class="btn btn-outline" style="width: 100%; text-align: center;">Choose Plan</a>
					</div>
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

@endsection
