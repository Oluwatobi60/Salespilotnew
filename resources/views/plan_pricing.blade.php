@extends('layout.layout')
@section('welcome_page_title')
Choose Your Plan - SalesPilot
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/style.css') }}">
<link rel="stylesheet" href="{{ asset('welcome_asset/plans.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="pricing" style="padding: 80px 20px;">
    <div class="container" style="max-width: 1400px; margin: 0 auto;">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: 36px; color: #333; margin-bottom: 15px;">Choose Your Perfect Plan</h2>
            <p style="font-size: 18px; color: #666;">Select a plan that fits your business needs</p>
        </div>

        <!-- Duration Selector -->
        <div style="text-align: center; margin-bottom: 40px;">
            <div class="duration-selector" style="display: inline-flex; background: #f5f5f5; padding: 5px; border-radius: 50px; gap: 5px;">
                <button type="button" class="duration-btn active" data-months="1" style="padding: 12px 30px; border: none; border-radius: 50px; background: #4CAF50; color: white; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    1 Month
                </button>
                <button type="button" class="duration-btn" data-months="3" style="padding: 12px 30px; border: none; border-radius: 50px; background: transparent; color: #666; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    3 Months
                    <span style="display: block; font-size: 12px; font-weight: 400;">Save 5%</span>
                </button>
                <button type="button" class="duration-btn" data-months="6" style="padding: 12px 30px; border: none; border-radius: 50px; background: transparent; color: #666; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    6 Months
                    <span style="display: block; font-size: 12px; font-weight: 400;">Save 10%</span>
                </button>
                <button type="button" class="duration-btn" data-months="12" style="padding: 12px 30px; border: none; border-radius: 50px; background: transparent; color: #666; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    1 Year
                    <span style="display: block; font-size: 12px; font-weight: 400;">Save 15%</span>
                </button>
            </div>
        </div>

        <div class="pricing-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px;">
            <!-- Free Plan -->
            <div class="pricing-card" style="background: white; border-radius: 12px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; border: 2px solid #e0e0e0; transition: transform 0.3s ease;">
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
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="free">
                    <input type="hidden" name="duration" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Get Started
                    </button>
                </form>
            </div>



              <!-- Basic Plan -->
            <div class="pricing-card popular" style="background: white; border-radius: 12px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); text-align: center; border: 2px solid #4CAF50; position: relative;">
                <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #4CAF50; color: white; padding: 5px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                    Most Popular
                </div>
                <h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Basic</h3>
                <div class="price" style="margin: 20px 0;" data-monthly-price="5000">
                    <div style="margin-bottom: 10px;">
                        <span class="calculated-price" style="font-size: 48px; font-weight: bold; color: #4CAF50;">₦14,250</span>
                    </div>
                    <div style="font-size: 14px; color: #999; text-decoration: line-through;" class="original-price">₦15,000</div>
                    <div style="font-size: 16px; color: #4CAF50; font-weight: 600; margin-top: 5px;" class="duration-text">for 3 months</div>
                </div>
                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 1 Manager/Administrator Account</li>
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 2 Staff Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Advanced Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Sales & Purchase Tracking</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Basic Reports & Analytics</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Priority Email Support</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="basic">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>
            </div>


            <!-- Standard Plan -->
            <div class="pricing-card popular" style="background: white; border-radius: 12px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); text-align: center; border: 2px solid #4CAF50; position: relative;">
                <div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #4CAF50; color: white; padding: 5px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                    Most Popular
                </div>
                <h3 style="font-size: 24px; color: #333; margin-bottom: 10px;">Standard</h3>
                <div class="price" style="margin: 20px 0;" data-monthly-price="10000">
                    <div style="margin-bottom: 10px;">
                        <span class="calculated-price" style="font-size: 48px; font-weight: bold; color: #4CAF50;">₦28,500</span>
                    </div>
                    <div style="font-size: 14px; color: #999; text-decoration: line-through;" class="original-price">₦30,000</div>
                    <div style="font-size: 16px; color: #4CAF50; font-weight: 600; margin-top: 5px;" class="duration-text">for 3 months</div>
                </div>
                <ul style="list-style: none; padding: 0; margin: 30px 0; text-align: left;">
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ 2 Manager/Administrator Accounts</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Up to 4 Staff Accounts</li>
                     <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Allows 2 branches</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Advanced Inventory Management</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Sales & Purchase Tracking</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Basic Reports & Analytics</li>
                    <li style="padding: 10px 0; color: #666; border-bottom: 1px solid #f0f0f0;">✓ Priority Email Support</li>
                </ul>
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="standard">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-card" style="background: white; border-radius: 12px; padding: 40px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; border: 2px solid #e0e0e0; transition: transform 0.3s ease;">
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
                <form action="{{ route('select.plan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan" value="premium">
                    <input type="hidden" name="duration" class="duration-input" value="1">
                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Choose Plan
                    </button>
                </form>
            </div>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <p style="color: #666; margin-bottom: 15px;">Need help choosing? <a href="#" style="color: #4CAF50; text-decoration: none; font-weight: 600;">Contact our sales team</a></p>
        </div>
    </div>
</section>

<script>
// Price calculation logic
const discounts = {
    1: 0,     // 0% discount for 1 month
    3: 0.05,  // 5% discount for 3 months
    6: 0.10,  // 10% discount for 6 months
    12: 0.15  // 15% discount for 1 year
};

function formatCurrency(amount) {
    return '₦' + amount.toLocaleString('en-NG');
}

function updatePrices(months) {
    const priceElements = document.querySelectorAll('.price[data-monthly-price]');

    priceElements.forEach(priceEl => {
        const monthlyPrice = parseInt(priceEl.dataset.monthlyPrice);
        const totalWithoutDiscount = monthlyPrice * months;
        const discount = discounts[months];
        const totalWithDiscount = totalWithoutDiscount * (1 - discount);

        // Update calculated price
        const calculatedPrice = priceEl.querySelector('.calculated-price');
        calculatedPrice.textContent = formatCurrency(totalWithDiscount);

        // Update original price
        const originalPrice = priceEl.querySelector('.original-price');
        if (discount > 0) {
            originalPrice.textContent = formatCurrency(totalWithoutDiscount);
            originalPrice.style.display = 'block';
        } else {
            originalPrice.style.display = 'none';
        }

        // Update duration text
        const durationText = priceEl.querySelector('.duration-text');
        const durationLabel = months === 1 ? '1 month' : (months === 12 ? '1 year' : `${months} months`);
        durationText.textContent = `for ${durationLabel}`;
    });

    // Update all duration inputs
    document.querySelectorAll('.duration-input').forEach(input => {
        input.value = months;
    });
}

// Duration button click handlers
document.querySelectorAll('.duration-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.duration-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = 'transparent';
            b.style.color = '#666';
        });

        // Add active class to clicked button
        this.classList.add('active');
        this.style.background = '#4CAF50';
        this.style.color = 'white';

        // Update prices
        const months = parseInt(this.dataset.months);
        updatePrices(months);
    });
});

// Initialize with 1 month selected
updatePrices(1);
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#4CAF50',
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
        confirmButtonColor: '#f44336'
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
        confirmButtonColor: '#f44336',
        width: '500px'
    });
</script>
@endif


@endsection
