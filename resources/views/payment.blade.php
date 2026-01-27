@extends('layout.layout')
@section('welcome_page_title')
Payment - SalesPilot
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/style.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<section class="payment-section" style="padding: 80px 20px; min-height: 100vh; background: #f5f5f5;">
    <div class="container" style="max-width: 800px; margin: 0 auto;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="font-size: 32px; color: #333; margin-bottom: 10px;">Complete Your Payment</h2>
            <p style="font-size: 16px; color: #666;">You're one step away from accessing SalesPilot</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Order Summary -->
            <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <h3 style="font-size: 20px; color: #333; margin-bottom: 20px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">Order Summary</h3>

                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Plan:</span>
                        <span style="font-weight: 600; color: #333; text-transform: capitalize;">{{ $plan->name }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Duration:</span>
                        <span style="font-weight: 600; color: #333;">{{ $duration == 1 ? '1 Month' : ($duration == 12 ? '1 Year' : $duration . ' Months') }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Monthly Price:</span>
                        <span style="color: #333;">₦{{ number_format($plan->monthly_price, 2) }}</span>
                    </div>

                    @if($pricing['discount_percentage'] > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #666;">Subtotal:</span>
                        <span style="color: #999; text-decoration: line-through;">₦{{ number_format($pricing['original_price'], 2) }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                        <span style="color: #4CAF50; font-weight: 600;">Discount ({{ $pricing['discount_percentage'] }}%):</span>
                        <span style="color: #4CAF50; font-weight: 600;">-₦{{ number_format($pricing['savings'], 2) }}</span>
                    </div>
                    @endif

                    <div style="border-top: 2px solid #e0e0e0; margin-top: 20px; padding-top: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 18px; font-weight: 600; color: #333;">Total Amount:</span>
                            <span style="font-size: 28px; font-weight: bold; color: #4CAF50;">₦{{ number_format($pricing['discounted_price'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 20px;">
                    <p style="font-size: 14px; color: #666; margin: 0;">
                        <strong>Plan Features:</strong><br>
                        • {{ $plan->max_managers }} Manager Account(s)<br>
                        • {{ $plan->max_staff ? $plan->max_staff . ' Staff Accounts' : 'Unlimited Staff' }}<br>
                        • {{ $plan->max_branches ? $plan->max_branches . ' Branch(es)' : 'Multi-branch Support' }}
                    </p>
                </div>
            </div>

            <!-- Payment Form -->
            <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <h3 style="font-size: 20px; color: #333; margin-bottom: 20px; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">Payment Method</h3>

                <form action="{{ route('payment.process') }}" method="POST" id="paymentForm">
                    @csrf

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; color: #333; font-weight: 600; margin-bottom: 10px;">
                            <input type="radio" name="payment_method" value="bank_transfer" checked style="margin-right: 8px;">
                            Bank Transfer
                        </label>
                        <div id="bankDetails" style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 10px;">
                            <p style="font-size: 14px; color: #333; margin: 5px 0;"><strong>Bank:</strong> GTBank</p>
                            <p style="font-size: 14px; color: #333; margin: 5px 0;"><strong>Account Name:</strong> SalesPilot Technologies</p>
                            <p style="font-size: 14px; color: #333; margin: 5px 0;"><strong>Account Number:</strong> 0123456789</p>
                            <p style="font-size: 12px; color: #666; margin-top: 10px;">Please use your email as payment reference</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <label style="display: block; color: #333; font-weight: 600; margin-bottom: 10px;">
                            <input type="radio" name="payment_method" value="paystack" style="margin-right: 8px;">
                            Paystack (Card Payment)
                        </label>
                        <div id="paystackInfo" style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-top: 10px; display: none;">
                            <p style="font-size: 14px; color: #666;">You will be redirected to Paystack to complete your payment securely.</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; color: #333; font-weight: 600; margin-bottom: 8px;">Payment Reference (Optional)</label>
                        <input type="text" name="payment_reference" placeholder="Enter payment reference" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                        <small style="color: #666; font-size: 12px;">If you've already made payment, enter the reference here</small>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; color: #666; font-size: 14px;">
                            <input type="checkbox" required style="margin-right: 8px;">
                            I agree to the <a href="#" style="color: #4CAF50; text-decoration: none;">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.3s ease;">
                        Complete Payment
                    </button>

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="{{ route('plan_pricing') }}" style="color: #666; text-decoration: none; font-size: 14px;">← Back to Plans</a>
                    </div>
                </form>
            </div>
        </div>

        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-top: 30px;">
            <p style="margin: 0; color: #856404; font-size: 14px;">
                <strong>Note:</strong> This is a temporary payment page. After making a bank transfer, click "Complete Payment" and our team will verify your payment within 24 hours. You will receive an email confirmation once verified.
            </p>
        </div>
    </div>
</section>

<script>
// Show/hide payment method details
document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('bankDetails').style.display = this.value === 'bank_transfer' ? 'block' : 'none';
        document.getElementById('paystackInfo').style.display = this.value === 'paystack' ? 'block' : 'none';
    });
});

// Form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

    if (paymentMethod === 'paystack') {
        e.preventDefault();
        Swal.fire({
            icon: 'info',
            title: 'Coming Soon',
            text: 'Paystack integration is coming soon. Please use Bank Transfer for now.',
            confirmButtonColor: '#4CAF50'
        });
        return false;
    }
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#4CAF50'
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

<style>
@media screen and (max-width: 768px) {
    .container > div:first-child {
        grid-template-columns: 1fr !important;
    }
}

button[type="submit"]:hover {
    background: #45a049 !important;
}
</style>
@endsection
