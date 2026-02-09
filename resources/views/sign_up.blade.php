@extends('layout.layout')
@section('welcome_page_title')
Get Started here
@endsection
@section('welcome_page_content')
<link rel="stylesheet" href="{{ asset('welcome_asset/signup.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

 <section class="home">
  <div class="main_content_wrapper" style="width:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;">
    <div class="form_container" style="position:relative;overflow:hidden;">
      <!-- Login Form -->
      <div class="form login_form">

    <form action="{{ route('get_started.store') }}" method="post" style="width:100%;" id="emailForm">
        @csrf
      <h3 style="text-align:center;margin-bottom:18px;">Input a valid E-mail</h3>

      <!-- Email Verified Badge -->
      @if(session('email_verified'))
      <div style="text-align:center;margin:15px 0;padding:12px;background:#10b981;border-radius:8px;">
        <p style="margin:0;color:#ffffff;font-weight:600;font-size:16px;">
          <i class="uil uil-check-circle"></i> EMAIL VERIFIED
        </p>
        <p style="margin:5px 0 0 0;color:#ffffff;font-size:14px;">
          Please complete your registration
        </p>
      </div>
      @endif

      <div class="input_box">
      <input type="email" name="email" id="emailInput" placeholder="Enter your email" required />
            <i class="uil uil-envelope-alt email"></i>
          </div>

          <!-- Countdown Timer Display -->
          <div id="countdownDisplay" class="d-none" style="text-align:center;margin:15px 0;padding:10px;background:#f0f9ff;border-radius:8px;">
            <p style="margin:0;color:#1e40af;font-weight:500;">
              <i class="uil uil-clock"></i> Link expires in: <span id="countdown" style="font-weight:700;color:#dc2626;">30:00</span>
            </p>
          </div>

          <button class="button" type="submit" id="proceedEmailBtn">Proceed</button>

          <!-- Resend Link Button -->
          <div id="resendSection" class="d-none" style="text-align:center;margin-top:15px;">
            <button type="button" class="button" id="resendBtn" style="background:#f59e0b;" disabled>
              <i class="uil uil-redo"></i> Resend Verification Link
            </button>
          </div>

          <div class="login_signup">A verification link will be sent to your E-mail, click the link and continue the sign up process</div>
        </form>
      </div>
    </div>
  </div>
    </section>

<script>
    let countdownInterval = null;
    let verificationCheckInterval = null;
    let currentEmail = '';

    // Function to check if email has been verified
    function checkVerificationStatus() {
        if (!currentEmail) return;

        fetch('{{ route('get_started.check_verification') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: currentEmail })
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                // Stop countdown and verification check
                if (countdownInterval) clearInterval(countdownInterval);
                if (verificationCheckInterval) clearInterval(verificationCheckInterval);

                // Hide countdown and resend section
                document.getElementById('countdownDisplay').classList.add('d-none');
                document.getElementById('resendSection').classList.add('d-none');
                document.getElementById('proceedEmailBtn').classList.add('d-none');
                document.getElementById('emailInput').disabled = true;
                document.getElementById('emailInput').style.opacity = "0.5";

                // Show EMAIL VERIFIED badge
                const verifiedBadge = document.getElementById('emailVerifiedBadge');
                verifiedBadge.classList.remove('d-none');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Email Verified!',
                    text: 'Your email has been successfully verified. Please complete your registration.',
                    confirmButtonColor: '#667eea',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });

                // Clear session storage
                sessionStorage.removeItem('verificationToken');
            }
        })
        .catch(error => {
            console.error('Error checking verification status:', error);
        });
    }

    // Countdown Timer Function
    function startCountdown(expiresAt) {
        const countdownDisplay = document.getElementById('countdownDisplay');
        const countdownElement = document.getElementById('countdown');
        const resendSection = document.getElementById('resendSection');
        const resendBtn = document.getElementById('resendBtn');
        const proceedBtn = document.getElementById('proceedEmailBtn');

        countdownDisplay.classList.remove('d-none');

        // Disable resend button while countdown is active
        resendBtn.disabled = true;
        resendBtn.style.opacity = "0.5";
        resendBtn.style.cursor = "not-allowed";

        // Start checking verification status every 3 seconds
        verificationCheckInterval = setInterval(checkVerificationStatus, 3000);

        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const expireTime = new Date(expiresAt).getTime();
            const distance = expireTime - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = "EXPIRED";
                countdownElement.style.color = "#dc2626";
                resendSection.classList.remove('d-none');

                // Enable resend button when expired
                resendBtn.disabled = false;
                resendBtn.style.opacity = "1";
                resendBtn.style.cursor = "pointer";

                proceedBtn.disabled = true;
                proceedBtn.style.opacity = "0.5";
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML =
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);

            // Change color when less than 5 minutes
            if (minutes < 5) {
                countdownElement.style.color = "#dc2626";
            }
        }, 1000);
    }

    // Don't auto-start countdown on page load - only after Proceed is clicked

    // Handle form submission
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const email = formData.get('email');
        currentEmail = email;

        fetch('{{ route('get_started.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store token data in session storage
                const tokenData = {
                    email: email,
                    expires_at: data.expires_at,
                    sent_at: new Date().toISOString()
                };
                sessionStorage.setItem('verificationToken', JSON.stringify(tokenData));

                // Start countdown
                startCountdown(data.expires_at);

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'OK',
                    timer: 5000,
                    timerProgressBar: true,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Something went wrong',
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'OK'
            });
        });
    });

    // Handle resend button
    document.getElementById('resendBtn').addEventListener('click', function() {
        if (!currentEmail) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please enter your email first',
                confirmButtonColor: '#667eea',
            });
            return;
        }

        // Disable button during request
        this.disabled = true;
        this.innerHTML = '<i class="uil uil-spinner-alt"></i> Sending...';

        fetch('{{ route('get_started.resend') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: currentEmail })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update session storage with new expiration
                const tokenData = {
                    email: currentEmail,
                    expires_at: data.expires_at,
                    sent_at: new Date().toISOString()
                };
                sessionStorage.setItem('verificationToken', JSON.stringify(tokenData));

                // Reset UI - hide resend section and re-enable proceed button
                document.getElementById('resendSection').classList.add('d-none');
                document.getElementById('proceedEmailBtn').disabled = false;
                document.getElementById('proceedEmailBtn').style.opacity = "1";

                // Clear old countdown and start new one
                if (countdownInterval) clearInterval(countdownInterval);
                startCountdown(data.expires_at);

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonColor: '#667eea',
                    timer: 5000,
                    timerProgressBar: true,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to resend verification link',
                    confirmButtonColor: '#667eea',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                confirmButtonColor: '#667eea',
            });
        })
        .finally(() => {
            // Re-enable button
            this.disabled = false;
            this.innerHTML = '<i class="uil uil-redo"></i> Resend Verification Link';
        });
    });

    // Display success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK',
            timer: 5000,
            timerProgressBar: true,
        });
    @endif

    // Display error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK'
        });
    @endif

    // Display validation errors
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'OK'
        });
    @endif
</script>

@endsection
