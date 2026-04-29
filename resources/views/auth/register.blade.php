<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Your SalesPilot Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="{{ asset('welcome_asset/register.css') }}">
    <link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Branding -->
        <div class="register-left">
            <div class="register-branding">
                <div class="brand-logo-wrapper">
                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="SalesPilot Logo" class="brand-logo-img">
                </div>
                <h1>Join SalesPilot Today</h1>
                <p>Start managing your business with powerful tools designed for growth and success.</p>

                <div class="progress-steps">
                    <div class="step-item">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Create Account</h4>
                            <p>Fill in your details</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Setup Business</h4>
                            <p>Configure your store</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Start Selling</h4>
                            <p>Grow your business</p>
                        </div>
                    </div>
                </div>

                <div class="benefits-list">
                    <div class="benefit-item">
                        <i class="uil uil-check-circle"></i>
                        <span>Free to get started</span>
                    </div>
                    <div class="benefit-item">
                        <i class="uil uil-check-circle"></i>
                        <span>Real-time inventory tracking</span>
                    </div>
                    <div class="benefit-item">
                        <i class="uil uil-check-circle"></i>
                        <span>Powerful analytics dashboard</span>
                    </div>
                    <div class="benefit-item">
                        <i class="uil uil-check-circle"></i>
                        <span>Multi-branch support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="register-right">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2>Create Your Account</h2>
                    <p>Get started with SalesPilot in just a few minutes</p>
                </div>

                <div class="form-card">
                    <form action="{{ route('register') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @csrf

                        <!-- Personal Information Section -->
                        <div class="section-title">
                            <i class="uil uil-user-circle"></i>
                            Personal Information
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="first_name">First Name <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-user"></i>
                                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="{{ old('first_name') }}" required />
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="surname">Surname <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-user"></i>
                                    <input type="text" id="surname" name="surname" placeholder="Enter your surname" value="{{ old('surname') }}" required />
                                </div>
                            </div>

                            <div class="input-group form-grid-full">
                                <label for="other_name">Other Name(s)</label>
                                <div class="input-wrapper">
                                    <i class="uil uil-user"></i>
                                    <input type="text" id="other_name" name="other_name" placeholder="Enter other names (optional)" value="{{ old('other_name') }}" />
                                </div>
                            </div>
                        </div>

                        <!-- Business Information Section -->
                        <div class="section-title">
                            <i class="uil uil-store"></i>
                            Business Information
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="business_name">Business Name <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-briefcase"></i>
                                    <input type="text" id="business_name" name="business_name" placeholder="Enter business name" value="{{ old('business_name') }}" required />
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="branch_name">Branch Name <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-sitemap"></i>
                                    <input type="text" id="branch_name" name="branch_name" placeholder="Enter branch name" value="{{ old('branch_name') }}" required />
                                </div>
                            </div>

                            <div class="input-group form-grid-full">
                                <label for="businessLogo">Business Logo</label>
                                <div class="file-upload-wrapper">
                                    <i class="uil uil-image" style="position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); z-index: 2; color: #667eea;"></i>
                                    <label for="businessLogo" class="file-upload-label">
                                        <span id="logoPlaceholder">Upload business logo (optional)</span>
                                        <input id="businessLogo" name="business_logo" type="file" accept="image/*" class="file-upload-input" />
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information Section -->
                        <div class="section-title">
                            <i class="uil uil-location-point"></i>
                            Location Information
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="stateSelect">State <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-map"></i>
                                    <select id="stateSelect" name="state" required>
                                        <option value="">Select State</option>
                                        <option value="Abia" {{ old('state') == 'Abia' ? 'selected' : '' }}>Abia</option>
                                        <option value="Adamawa" {{ old('state') == 'Adamawa' ? 'selected' : '' }}>Adamawa</option>
                                        <option value="Akwa Ibom" {{ old('state') == 'Akwa Ibom' ? 'selected' : '' }}>Akwa Ibom</option>
                                        <option value="Anambra" {{ old('state') == 'Anambra' ? 'selected' : '' }}>Anambra</option>
                                        <option value="Bauchi" {{ old('state') == 'Bauchi' ? 'selected' : '' }}>Bauchi</option>
                                        <option value="Bayelsa" {{ old('state') == 'Bayelsa' ? 'selected' : '' }}>Bayelsa</option>
                                        <option value="Benue" {{ old('state') == 'Benue' ? 'selected' : '' }}>Benue</option>
                                        <option value="Borno" {{ old('state') == 'Borno' ? 'selected' : '' }}>Borno</option>
                                        <option value="Cross River" {{ old('state') == 'Cross River' ? 'selected' : '' }}>Cross River</option>
                                        <option value="Delta" {{ old('state') == 'Delta' ? 'selected' : '' }}>Delta</option>
                                        <option value="Ebonyi" {{ old('state') == 'Ebonyi' ? 'selected' : '' }}>Ebonyi</option>
                                        <option value="Edo" {{ old('state') == 'Edo' ? 'selected' : '' }}>Edo</option>
                                        <option value="Ekiti" {{ old('state') == 'Ekiti' ? 'selected' : '' }}>Ekiti</option>
                                        <option value="Enugu" {{ old('state') == 'Enugu' ? 'selected' : '' }}>Enugu</option>
                                        <option value="FCT" {{ old('state') == 'FCT' ? 'selected' : '' }}>FCT - Abuja</option>
                                        <option value="Gombe" {{ old('state') == 'Gombe' ? 'selected' : '' }}>Gombe</option>
                                        <option value="Imo" {{ old('state') == 'Imo' ? 'selected' : '' }}>Imo</option>
                                        <option value="Jigawa" {{ old('state') == 'Jigawa' ? 'selected' : '' }}>Jigawa</option>
                                        <option value="Kaduna" {{ old('state') == 'Kaduna' ? 'selected' : '' }}>Kaduna</option>
                                        <option value="Kano" {{ old('state') == 'Kano' ? 'selected' : '' }}>Kano</option>
                                        <option value="Katsina" {{ old('state') == 'Katsina' ? 'selected' : '' }}>Katsina</option>
                                        <option value="Kebbi" {{ old('state') == 'Kebbi' ? 'selected' : '' }}>Kebbi</option>
                                        <option value="Kogi" {{ old('state') == 'Kogi' ? 'selected' : '' }}>Kogi</option>
                                        <option value="Kwara" {{ old('state') == 'Kwara' ? 'selected' : '' }}>Kwara</option>
                                        <option value="Lagos" {{ old('state') == 'Lagos' ? 'selected' : '' }}>Lagos</option>
                                        <option value="Nasarawa" {{ old('state') == 'Nasarawa' ? 'selected' : '' }}>Nasarawa</option>
                                        <option value="Niger" {{ old('state') == 'Niger' ? 'selected' : '' }}>Niger</option>
                                        <option value="Ogun" {{ old('state') == 'Ogun' ? 'selected' : '' }}>Ogun</option>
                                        <option value="Ondo" {{ old('state') == 'Ondo' ? 'selected' : '' }}>Ondo</option>
                                        <option value="Osun" {{ old('state') == 'Osun' ? 'selected' : '' }}>Osun</option>
                                        <option value="Oyo" {{ old('state') == 'Oyo' ? 'selected' : '' }}>Oyo</option>
                                        <option value="Plateau" {{ old('state') == 'Plateau' ? 'selected' : '' }}>Plateau</option>
                                        <option value="Rivers" {{ old('state') == 'Rivers' ? 'selected' : '' }}>Rivers</option>
                                        <option value="Sokoto" {{ old('state') == 'Sokoto' ? 'selected' : '' }}>Sokoto</option>
                                        <option value="Taraba" {{ old('state') == 'Taraba' ? 'selected' : '' }}>Taraba</option>
                                        <option value="Yobe" {{ old('state') == 'Yobe' ? 'selected' : '' }}>Yobe</option>
                                        <option value="Zamfara" {{ old('state') == 'Zamfara' ? 'selected' : '' }}>Zamfara</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="lgaSelect">Local Government Area <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-map-marker"></i>
                                    <select id="lgaSelect" name="local_govt" required>
                                        <option value="">Select Local Government Area</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-group form-grid-full">
                                <label for="address">Address <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-location-point"></i>
                                    <textarea id="address" name="address" placeholder="Number, Street, City" required rows="3">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information Section -->
                        <div class="section-title">
                            <i class="uil uil-phone"></i>
                            Contact Information
                        </div>

                        <div class="form-grid">
                            <div class="input-group">
                                <label for="phoneInput">Phone Number <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-phone"></i>
                                    <input type="tel" id="phoneInput" name="phone_number" placeholder="Enter 11-digit phone number" required pattern="[0-9]{11}" maxlength="11" title="Please enter exactly 11 digits" value="{{ old('phone_number') }}" />
                                </div>
                            </div>

                            <div class="input-group">
                                <label for="email">Email Address <span style="color: #dc2626;">*</span></label>
                                <div class="input-wrapper">
                                    <i class="uil uil-envelope-alt"></i>
                                    <input type="email" id="email" name="email" value="{{ old('email', $signup_email ?? '') }}" placeholder="Verified Email" required readonly />
                                </div>
                            </div>
                        </div>

                        <!-- Referral Information Section -->
                        <div class="section-title">
                            <i class="uil uil-user-plus"></i>
                            Referral Information (Optional)
                        </div>

                        <div class="form-grid">
                            <div class="input-group form-grid-full">
                                <label for="referralcode">BRM Referral Code</label>
                                <div class="input-wrapper">
                                    <i class="uil uil-user-plus"></i>
                                    <input type="text" id="referralcode" name="referral_code" placeholder="Enter BRM referral code (optional)" maxlength="255" title="Enter BRM referral code if you have one" value="{{ old('referral_code') }}" />
                                    <div id="referralSpinner">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </div>
                                </div>
                                <div id="referralFeedback"></div>
                                @if ($errors->has('referral_code'))
                                    <div style="color: #d32f2f; font-size: 0.85rem; margin-top: 0.5rem;">
                                        <i class="uil uil-exclamation-triangle"></i> {{ $errors->first('referral_code') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="uil uil-info-circle"></i>
                            <div>
                                <strong>BRM Referral Code:</strong> If you were referred by a Business Relation Manager, enter their referral code to associate your account with them. The code will be verified in real-time.
                            </div>
                        </div>

                        <!-- Hidden role field -->
                        <input type="hidden" name="role" value="manager" />

                        <button class="btn-primary-custom btn-loading" type="submit" id="signupSubmitBtn" data-loading-text="Creating account...">
                            <span class="btn-text">Create Account</span>
                            <span class="btn-spinner"></span>
                        </button>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('welcome_asset/js/register_lg.js') }}"></script>
    <script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true,
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK'
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
            confirmButtonText: 'OK',
            width: '500px'
        });
    </script>
    @endif

    <!-- File Upload Preview -->
    <script>
        document.getElementById('businessLogo').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Upload business logo (optional)';
            document.getElementById('logoPlaceholder').textContent = fileName;
        });
    </script>

    <!-- Real-time Referral Code Verification -->
    <script>
    (function() {
        const referralInput = document.getElementById('referralcode');
        const spinner = document.getElementById('referralSpinner');
        const feedback = document.getElementById('referralFeedback');
        const submitBtn = document.getElementById('signupSubmitBtn');
        let verificationTimeout;

        // Listen for input changes
        referralInput.addEventListener('input', function() {
            clearTimeout(verificationTimeout);
            const code = this.value.trim();

            // Hide feedback if input is empty
            if (!code) {
                spinner.style.display = 'none';
                feedback.style.display = 'none';
                if (submitBtn) submitBtn.disabled = false;
                return;
            }

            // Show spinner while verifying
            spinner.style.display = 'inline-block';
            feedback.style.display = 'none';

            // Debounce the verification (wait 800ms after user stops typing)
            verificationTimeout = setTimeout(() => {
                verifyReferralCode(code);
            }, 800);
        });

        function verifyReferralCode(code) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                             document.querySelector('input[name="_token"]')?.value;

            fetch('{{ route("verify.referral_code") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    referral_code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                feedback.style.display = 'block';

                if (data.valid) {
                    // Success: Valid referral code
                    feedback.innerHTML = `
                        <i class="uil uil-check-circle" style="color: #4CAF50;"></i>
                        <span style="color: #2e7d32;">Referred by: <strong>${data.brm_name}</strong></span>
                    `;
                    feedback.style.backgroundColor = '#e8f5e9';
                    feedback.style.borderLeft = '4px solid #4CAF50';
                    if (submitBtn) submitBtn.disabled = false;
                } else {
                    // Error: Invalid referral code
                    feedback.innerHTML = `
                        <i class="uil uil-times-circle" style="color: #d32f2f;"></i>
                        <span style="color: #d32f2f;"><strong>Invalid!</strong> ${data.message}</span>
                    `;
                    feedback.style.backgroundColor = '#ffebee';
                    feedback.style.borderLeft = '4px solid #d32f2f';
                    if (submitBtn) submitBtn.disabled = false; // Allow submission to show validation error
                }
            })
            .catch(error => {
                console.error('Error verifying referral code:', error);
                spinner.style.display = 'none';
                feedback.innerHTML = `
                    <i class="uil uil-exclamation-triangle" style="color: #ff9800;"></i>
                    <span style="color: #ff9800;"><strong>Error!</strong> Could not verify code. Please try again.</span>
                `;
                feedback.style.display = 'block';
                feedback.style.backgroundColor = '#fff3e0';
                feedback.style.borderLeft = '4px solid #ff9800';
            });
        }
    })();
    </script>
</body>
</html>
