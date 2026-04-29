@extends('layout.layout')
@section('welcome_page_title')
Choose Your Plan - SalesPilot
@endsection
@if(auth()->check() && (!auth()->user()->password_set || isset($activeSubscription)))
    @section('hide_nav_links') 1 @endsection
    @if(!auth()->user()->password_set)
        @section('brand_bar_step')<span class="sp-brand-step">Step 2 of 3 &mdash; Choose a Plan</span>@endsection
    @else
        @section('brand_bar_step')<span class="sp-brand-step">Upgrade Your Plan</span>@endsection
    @endif
@endif
@section('welcome_page_content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<link rel="stylesheet" href="{{ asset('welcome_asset/pricing-responsive.css') }}">
<link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<section class="pricing-section">
    <div class="pricing-container">
    <div class="pricing-container">
        <div class="section-header">
            <h2>Choose Your Perfect Plan</h2>
            <p>Select a plan that fits your business needs and start growing today</p>
        </div>

        @if(isset($activeSubscription))
        <div style="max-width: 800px; margin: 0 auto 2rem; padding: 1.25rem; background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); border-left: 4px solid #0ea5e9; border-radius: 12px;">
            <div style="display: flex; align-items: start; gap: 1rem;">
                <i class="uil uil-info-circle" style="font-size: 1.5rem; color: #0284c7; flex-shrink: 0;"></i>
                <div>
                    <h4 style="margin: 0 0 0.5rem 0; color: #0369a1; font-size: 1.1rem; font-weight: 700;">Current Subscription</h4>
                    <p style="margin: 0 0 0.5rem 0; color: #075985; font-size: 0.95rem;">
                        You're currently on the <strong>{{ ucfirst($activeSubscription->subscriptionPlan->name ?? 'N/A') }}</strong> plan 
                        (expires {{ $activeSubscription->end_date->format('M d, Y') }}).
                    </p>
                    <p style="margin: 0; color: #075985; font-size: 0.9rem;">
                        <i class="uil uil-check-circle" style="color: #10b981;"></i> 
                        Selecting a new plan will automatically cancel your current subscription and activate the new one immediately.
                    </p>
                </div>
            </div>
        </div>
        @endif

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

        <div class="pricing-grid">
            <!-- Free Plan -->
            <div class="pricing-card">
                <div class="plan-icon">
                    <i class="uil uil-gift" style="color: #3b82f6;"></i>
                </div>
                <h3>Free Trial</h3>
                <div class="price">
                    <span class="calculated-price">₦0</span>
                    <div class="duration-text">7-Days Free</div>
                    <p style="font-size: 0.875rem; color: #999; margin-top: 0.5rem;">Test all features risk-free</p>
                </div>
                <ul class="features-list">
                    <li><i class="uil uil-check-circle"></i> 1 Manager/Administrator Account</li>
                    <li><i class="uil uil-check-circle"></i> 1 Staff Account</li>
                    <li><i class="uil uil-check-circle"></i> Basic Inventory Management</li>
                    <li><i class="uil uil-check-circle"></i> Sales Tracking</li>
                    <li><i class="uil uil-check-circle"></i> Email Support</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="free">
                    <input type="hidden" name="duration" value="1">
                    <button type="submit" class="btn-plan btn-loading" data-loading-text="Starting trial...">
                    <span class="btn-text">Get Started Free</span>
                    <span class="btn-spinner"></span>
                    </button>

                    
                </form>
            </div>

            <!-- Basic Plan -->
            <div class="pricing-card popular">
                <div class="popular-badge">Most Popular</div>
                <div class="plan-icon">
                    <i class="uil uil-rocket" style="color: white;"></i>
                </div>
                <h3>Basic</h3>
                <div class="price" data-monthly-price="5000">
                    <span class="calculated-price">₦5,000</span>
                    <div class="original-price" style="display: none;">₦5,000</div>
                    <div class="duration-text">per month</div>
                </div>
                <ul class="features-list">
                    <li><i class="uil uil-check-circle"></i> 1 Manager/Administrator Account</li>
                    <li><i class="uil uil-check-circle"></i> 2 Staff Accounts</li>
                    <li><i class="uil uil-check-circle"></i> Advanced Inventory Management</li>
                    <li><i class="uil uil-check-circle"></i> Sales & Purchase Tracking</li>
                    <li><i class="uil uil-check-circle"></i> Basic Reports & Analytics</li>
                    <li><i class="uil uil-check-circle"></i> Priority Email Support</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="basic">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                  
                    <button type="submit" class="btn-plan btn-loading" data-loading-text="Starting Basic...">
                    <span class="btn-text">Choose Basic Plan</span>
                    <span class="btn-spinner"></span>
                    </button>
                </form>
            </div>

            <!-- Standard Plan -->
            <div class="pricing-card">
                <div class="plan-icon">
                    <i class="uil uil-star" style="color: #f59e0b;"></i>
                </div>
                <h3>Standard</h3>
                <div class="price" data-monthly-price="10000">
                    <span class="calculated-price">₦10,000</span>
                    <div class="original-price" style="display: none;">₦10,000</div>
                    <div class="duration-text">per month</div>
                </div>
                <ul class="features-list">
                    <li><i class="uil uil-check-circle"></i> 2 Manager/Administrator Accounts</li>
                    <li><i class="uil uil-check-circle"></i> Up to 4 Staff Accounts</li>
                    <li><i class="uil uil-check-circle"></i> Allows 2 branches</li>
                    <li><i class="uil uil-check-circle"></i> Advanced Inventory Management</li>
                    <li><i class="uil uil-check-circle"></i> Sales & Purchase Tracking</li>
                    <li><i class="uil uil-check-circle"></i> Advanced Reports & Analytics</li>
                    <li><i class="uil uil-check-circle"></i> Priority Email Support</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="standard">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    
                    <button type="submit" class="btn-plan btn-loading" data-loading-text="Starting Standard...">
                    <span class="btn-text">Choose Standard Plan</span>
                    <span class="btn-spinner"></span>
                    </button>
                </form>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card">
                <div class="plan-icon">
                    <i class="uil uil-diamond" style="color: #8b5cf6;"></i>
                </div>
                <h3>Premium</h3>
                <div class="price" data-monthly-price="20000">
                    <span class="calculated-price">₦20,000</span>
                    <div class="original-price" style="display: none;">₦20,000</div>
                    <div class="duration-text">per month</div>
                </div>
                <ul class="features-list">
                    <li><i class="uil uil-check-circle"></i> 3 Manager/Administrator Accounts</li>
                    <li><i class="uil uil-check-circle"></i> Unlimited Staff Accounts</li>
                    <li><i class="uil uil-check-circle"></i> Full Inventory Management</li>
                    <li><i class="uil uil-check-circle"></i> Advanced Reports & Analytics</li>
                    <li><i class="uil uil-check-circle"></i> Multi-branch Support</li>
                    <li><i class="uil uil-check-circle"></i> 24/7 Priority Support</li>
                    <li><i class="uil uil-check-circle"></i> Custom Integrations</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="premium">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                   
                    <button type="submit" class="btn-plan btn-loading" data-loading-text="Starting Premium...">
                    <span class="btn-text">Choose Premium Plan</span>
                    <span class="btn-spinner"></span>
                    </button>
                </form>
            </div>
        </div>

        <div class="help-section">
            <p>Need help choosing the right plan?</p>
            <a href="#">
                <i class="uil uil-comments-alt"></i> Contact our sales team
                <i class="uil uil-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>

<script>
    // Duration selector and pricing calculator
    document.addEventListener('DOMContentLoaded', function() {
        const durationButtons = document.querySelectorAll('.duration-btn');
        const pricingCards = document.querySelectorAll('.pricing-card');
        
        // Discount rates for different durations
        const discounts = {
            1: 0,      // No discount for 1 month
            3: 0.05,   // 5% discount for 3 months
            6: 0.10,   // 10% discount for 6 months
            12: 0.15   // 15% discount for 1 year
        };

        const durationText = {
            1: 'per month',
            3: 'for 3 months',
            6: 'for 6 months',
            12: 'for 1 year'
        };

        durationButtons.forEach(button => {
            button.addEventListener('click', function() {
                const selectedMonths = parseInt(this.getAttribute('data-months'));
                
                // Update active state
                durationButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Update pricing for each card
                pricingCards.forEach(card => {
                    const priceContainer = card.querySelector('.price');
                    if (!priceContainer) return;
                    
                    const monthlyPrice = parseInt(priceContainer.getAttribute('data-monthly-price'));
                    if (!monthlyPrice) return; // Skip Free plan
                    
                    const totalBeforeDiscount = monthlyPrice * selectedMonths;
                    const discount = discounts[selectedMonths];
                    const totalAfterDiscount = totalBeforeDiscount * (1 - discount);
                    
                    // Update calculated price
                    const calculatedPriceElement = priceContainer.querySelector('.calculated-price');
                    calculatedPriceElement.textContent = `₦${totalAfterDiscount.toLocaleString('en-NG', {maximumFractionDigits: 0})}`;
                    
                    // Update or hide original price
                    const originalPriceElement = priceContainer.querySelector('.original-price');
                    if (discount > 0) {
                        originalPriceElement.textContent = `₦${totalBeforeDiscount.toLocaleString('en-NG', {maximumFractionDigits: 0})}`;
                        originalPriceElement.style.display = 'block';
                    } else {
                        originalPriceElement.style.display = 'none';
                    }
                    
                    // Update duration text
                    const durationTextElement = priceContainer.querySelector('.duration-text');
                    durationTextElement.textContent = durationText[selectedMonths];
                    
                    // Update hidden duration input
                    const durationInput = card.querySelector('.duration-input');
                    if (durationInput) {
                        durationInput.value = selectedMonths;
                    }
                });
            });
        });
    });
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#667eea',
        timer: 5000,
        timerProgressBar: true
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#667eea'
    }).then((result) => {
        @if(session('redirect_url'))
        if (result.isConfirmed || result.isDismissed) {
            window.location.href = '{{ session('redirect_url') }}';
        }
        @endif
    });
</script>
@endif

@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        html: `
            <ul style="text-align: left; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        `,
        confirmButtonColor: '#667eea',
        width: '500px'
    });
</script>
@endif

@endsection
