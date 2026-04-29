@extends('layout.layout')
@section('welcome_page_title')
Payment - SalesPilot
@endsection
@section('hide_nav_links') 1 @endsection
@section('brand_bar_step')<span class="sp-brand-step">Step 2 of 3 &mdash; Payment</span>@endsection
@section('welcome_page_content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<link rel="stylesheet" href="{{ asset('welcome_asset/payment.css') }}">
<link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.paystack.co/v1/inline.js"></script>



<section class="payment-section">
    <div class="payment-container">
        <div class="payment-header">
            <h2>Complete Your Payment</h2>
            <p>You're one step away from accessing SalesPilot's powerful features</p>
        </div>

        <div class="payment-grid">
            <!-- Order Summary Card -->
            <div class="payment-card">
                <div class="card-header">
                    <i class="uil uil-file-info-alt"></i>
                    <h3>Order Summary</h3>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Plan:</span>
                    <span class="summary-value" style="text-transform: capitalize;">{{ $plan->name }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Duration:</span>
                    <span class="summary-value">{{ $duration == 1 ? '1 Month' : ($duration == 12 ? '1 Year' : $duration . ' Months') }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Monthly Price:</span>
                    <span class="summary-value price">₦{{ number_format($plan->monthly_price, 2) }}</span>
                </div>

                @if($pricing['discount_percentage'] > 0)
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value strikethrough">₦{{ number_format($pricing['original_price'], 2) }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Discount ({{ $pricing['discount_percentage'] }}%):</span>
                    <span class="summary-value discount">-₦{{ number_format($pricing['savings'], 2) }}</span>
                </div>
                @endif

                <div class="total-row">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="summary-label">Total Amount:</span>
                        <span class="total-amount">₦{{ number_format($pricing['discounted_price'], 2) }}</span>
                    </div>
                </div>

                <div class="features-box">
                    <h4><i class="uil uil-check-circle"></i> Plan Features</h4>
                    <ul class="features-list">
                        <li><i class="uil uil-check"></i> {{ $plan->max_managers }} Manager Account(s)</li>
                        <li><i class="uil uil-check"></i> {{ $plan->max_staff ? $plan->max_staff . ' Staff Accounts' : 'Unlimited Staff' }}</li>
                        <li><i class="uil uil-check"></i> {{ $plan->max_branches !== null ? ($plan->max_branches == 0 ? 'No Branch Support' : $plan->max_branches . ' Branch(es)') : 'Multi-branch Support' }}</li>
                    </ul>
                </div>
            </div>

            <!-- Payment Method Card -->
            <div class="payment-card">
                <div class="card-header">
                    <i class="uil uil-credit-card"></i>
                    <h3>Payment Method</h3>
                </div>

                <form action="{{ route('payment.process') }}" method="POST" id="paymentForm">
                    @csrf

                    <div class="payment-method">
                        <!-- Paystack Option -->
                        <div class="payment-option active" id="paystackOption">
                            <label>
                                <input type="radio" name="payment_method" value="paystack" checked>
                                <div class="payment-icon">
                                    <i class="uil uil-credit-card"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #333;">Card Payment</div>
                                    <div style="font-size: 0.85rem; color: #666; font-weight: 400;">Pay with debit/credit card</div>
                                </div>
                            </label>
                            <div class="payment-details show" id="paystackInfo">
                                <div class="info-badge">
                                    <i class="uil uil-shield-check"></i>
                                    <span>Secure payment processing via Paystack. Your card details are never stored.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Transfer Option -->
                        <div class="payment-option" id="bankOption">
                            <label>
                                <input type="radio" name="payment_method" value="bank_transfer">
                                <div class="payment-icon">
                                    <i class="uil uil-university"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #333;">Bank Transfer</div>
                                    <div style="font-size: 0.85rem; color: #666; font-weight: 400;">Direct transfer to our account</div>
                                </div>
                            </label>
                            <div class="payment-details" id="bankDetails">
                                <div class="bank-details">
                                    <div class="bank-detail-row">
                                        <span class="bank-detail-label">Bank:</span>
                                        <span class="bank-detail-value">GTBank</span>
                                    </div>
                                    <div class="bank-detail-row">
                                        <span class="bank-detail-label">Account Name:</span>
                                        <span class="bank-detail-value">SalesPilot Technologies</span>
                                    </div>
                                    <div class="bank-detail-row">
                                        <span class="bank-detail-label">Account Number:</span>
                                        <span class="bank-detail-value" style="font-weight: 700; color: #667eea;">0123456789</span>
                                    </div>
                                </div>
                                <div style="margin-top: 1rem; padding: 0.75rem; background: #fff3cd; border-radius: 8px; font-size: 0.85rem; color: #856404;">
                                    <i class="uil uil-info-circle"></i> Please use your email as payment reference
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="referenceField" style="display: none;">
                        <label for="payment_reference">Payment Reference</label>
                        <input type="text" id="payment_reference" name="payment_reference" placeholder="Enter payment reference">
                        <small>If you've already made payment, enter the reference here</small>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            I agree to the <a href="#">Terms & Conditions</a>
                        </label>
                    </div>

                    <button type="submit" id="paymentBtn" class="btn-payment btn-loading" data-loading-text="Processing payment...">
                        <span id="btnText" class="btn-text">Pay Now - ₦{{ number_format($pricing['discounted_price'], 2) }}</span>
                        <span class="btn-spinner"></span>
                    </button>


                    <div class="back-link">
                        <a href="{{ route('plan_pricing') }}">
                            <i class="uil uil-arrow-left"></i> Back to Plans
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="security-notice">
            <p>
                <i class="uil uil-shield-check"></i>
                <span><strong>Secure Payment:</strong> Your payment is processed securely. We use industry-standard encryption to protect your information.</span>
            </p>
        </div>
    </div>
    
</section>
<script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paystackOption = document.getElementById('paystackOption');
    const bankOption = document.getElementById('bankOption');
    const paystackInfo = document.getElementById('paystackInfo');
    const bankDetails = document.getElementById('bankDetails');
    const referenceField = document.getElementById('referenceField');
    const paymentForm = document.getElementById('paymentForm');
    const paymentBtn = document.getElementById('paymentBtn');
    const btnText = document.getElementById('btnText');
    
    const paystackRadio = document.querySelector('input[value="paystack"]');
    const bankRadio = document.querySelector('input[value="bank_transfer"]');

    // Handle payment option click
    paystackOption.addEventListener('click', function() {
        paystackRadio.checked = true;
        paystackOption.classList.add('active');
        bankOption.classList.remove('active');
        paystackInfo.classList.add('show');
        bankDetails.classList.remove('show');
        referenceField.style.display = 'none';
        btnText.textContent = 'Pay Now - ₦{{ number_format($pricing["discounted_price"], 2) }}';
    });

    bankOption.addEventListener('click', function() {
        bankRadio.checked = true;
        bankOption.classList.add('active');
        paystackOption.classList.remove('active');
        bankDetails.classList.add('show');
        paystackInfo.classList.remove('show');
        referenceField.style.display = 'block';
        btnText.textContent = 'Confirm Payment';
    });

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (paystackRadio.checked) {
            // Process with Paystack
            payWithPaystack();
        } else {
            // Submit form for bank transfer
            this.submit();
        }
    });

    function payWithPaystack() {
        const publicKey = '{{ $paystackPublicKey }}';
        
        if (!publicKey || publicKey === '') {
            Swal.fire({
                icon: 'error',
                title: 'Configuration Error',
                text: 'Paystack public key is not configured. Please contact support.',
                confirmButtonColor: '#667eea'
            });
            return;
        }
        
        const handler = PaystackPop.setup({
            key: publicKey,
            email: '{{ Auth::user()->email }}',
            amount: {{ $pricing['discounted_price'] * 100 }},
            currency: 'NGN',
            ref: 'SP_' + Math.floor((Math.random() * 1000000000) + 1),
            metadata: {
                custom_fields: [
                    {
                        display_name: "Customer Name",
                        variable_name: "customer_name",
                        value: "{{ Auth::user()->firstname }} {{ Auth::user()->surname }}"
                    },
                    {
                        display_name: "Plan",
                        variable_name: "plan",
                        value: "{{ $plan->name }}"
                    },
                    {
                        display_name: "Duration",
                        variable_name: "duration",
                        value: "{{ $duration }} months"
                    }
                ]
            },
            callback: function(response) {
                Swal.fire({
                    title: 'Processing Payment',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // Payment successful, redirect to callback URL
                window.location.href = '{{ route('payment.callback') }}?reference=' + response.reference;
            },
            onClose: function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Payment Cancelled',
                    text: 'You closed the payment window.',
                    confirmButtonColor: '#667eea'
                });
            }
        });

        handler.openIframe();
    }
});
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
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

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
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

@endsection
