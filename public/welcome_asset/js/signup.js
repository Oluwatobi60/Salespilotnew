
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

        // Disable resend link while countdown is active
        resendBtn.classList.add('disabled');

        // Start checking verification status every 3 seconds
        verificationCheckInterval = setInterval(checkVerificationStatus, 3000);

        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const expireTime = new Date(expiresAt).getTime();
            const distance = expireTime - now;

            if (distance < 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = "EXPIRED";
                countdownElement.classList.add('expired');
                resendSection.classList.remove('d-none');

                // Enable resend link when expired
                resendBtn.classList.remove('disabled');

                proceedBtn.disabled = true;
                return;
            }

            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML =
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);

            // Change color when less than 5 minutes
            if (minutes < 5) {
                countdownElement.classList.add('expired');
            } else {
                countdownElement.classList.remove('expired');
            }
        }, 1000);
    }

    // Handle form submission
    document.getElementById('emailForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const email = formData.get('email');
        currentEmail = email;

        // Disable button during submission
        const submitBtn = document.getElementById('proceedEmailBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span><i class="uil uil-spinner-alt"></i> Sending...</span>';

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

                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

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
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

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
    document.getElementById('resendBtn').addEventListener('click', function(e) {
        e.preventDefault();

        if (!currentEmail) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Please enter your email first',
                confirmButtonColor: '#667eea',
            });
            return;
        }

        // Disable link during request
        this.classList.add('disabled');
        const originalHTML = this.innerHTML;
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
            // Re-enable link
            this.classList.remove('disabled');
            this.innerHTML = originalHTML;
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
