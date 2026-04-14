/**
 * Real-time Password Validation for Add Staff Form
 * Validates password strength and confirmation match
 */

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const addStaffForm = document.getElementById('addStaffForm');

    if (!passwordInput || !passwordConfirmInput) return;

    // Create feedback elements if they don't exist
    if (!document.getElementById('passwordFeedback')) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.id = 'passwordFeedback';
        feedbackDiv.className = 'password-feedback mt-2';
        passwordInput.parentNode.appendChild(feedbackDiv);
    }

    if (!document.getElementById('confirmFeedback')) {
        const feedbackDiv = document.createElement('div');
        feedbackDiv.id = 'confirmFeedback';
        feedbackDiv.className = 'password-feedback mt-2';
        passwordConfirmInput.parentNode.appendChild(feedbackDiv);
    }

    // Add show password toggle buttons
    createPasswordToggle(passwordInput, 'togglePassword');
    createPasswordToggle(passwordConfirmInput, 'toggleConfirmPassword');

    // Password validation on input
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    // Confirm password validation on input
    passwordConfirmInput.addEventListener('input', function() {
        validatePasswordMatch();
    });

    // Form submission validation
    if (addStaffForm) {
        addStaffForm.addEventListener('submit', function(e) {
            if (!validatePasswordOnSubmit()) {
                e.preventDefault();
            }
        });
    }

    /**
     * Create a toggle button for showing/hiding password
     */
    function createPasswordToggle(inputElement, buttonId) {
        const parentContainer = inputElement.parentNode;
        const toggleBtn = document.createElement('button');

        toggleBtn.id = buttonId;
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn btn-sm password-toggle-btn';
        toggleBtn.title = 'Show Password';
        toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';

        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            togglePasswordVisibility(inputElement, toggleBtn);
        });

        // Wrap input with password container if not already wrapped
        if (!parentContainer.classList.contains('password-input-container')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'password-input-container position-relative';

            inputElement.parentNode.insertBefore(wrapper, inputElement);
            wrapper.appendChild(inputElement);
            wrapper.appendChild(toggleBtn);
        } else {
            parentContainer.appendChild(toggleBtn);
        }
    }

    /**
     * Toggle password visibility
     */
    function togglePasswordVisibility(inputElement, toggleBtn) {
        if (inputElement.type === 'password') {
            inputElement.type = 'text';
            toggleBtn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            toggleBtn.title = 'Hide Password';
            toggleBtn.classList.add('active');
        } else {
            inputElement.type = 'password';
            toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
            toggleBtn.title = 'Show Password';
            toggleBtn.classList.remove('active');
        }
    }

    /**
     * Validate password strength and requirements
     */
    function validatePassword(password) {
        const feedbackDiv = document.getElementById('passwordFeedback');
        const requirements = {
            minLength: password.length >= 8,
            hasLetters: /[a-zA-Z]/.test(password),
            hasNumbers: /\d/.test(password),
            noSpaces: !/\s/.test(password),
            mixed: /[a-z]/.test(password) && /[A-Z]/.test(password)
        };

        let strength = 0;
        let feedbackHTML = '<div class="password-requirements">';

        // Check minimum length (8 characters)
        if (password.length === 0) {
            feedbackDiv.innerHTML = '';
            return false;
        }

        if (requirements.minLength) {
            strength++;
            feedbackHTML += '<div class="requirement met"><i class="bi bi-check-circle-fill text-success me-2"></i>At least 8 characters</div>';
        } else {
            feedbackHTML += '<div class="requirement unmet"><i class="bi bi-circle me-2"></i>At least 8 characters</div>';
        }

        // Check for letters
        if (requirements.hasLetters) {
            strength++;
            feedbackHTML += '<div class="requirement met"><i class="bi bi-check-circle-fill text-success me-2"></i>Contains letters (a-z, A-Z)</div>';
        } else {
            feedbackHTML += '<div class="requirement unmet"><i class="bi bi-circle me-2"></i>Contains letters (a-z, A-Z)</div>';
        }

        // Check for numbers
        if (requirements.hasNumbers) {
            strength++;
            feedbackHTML += '<div class="requirement met"><i class="bi bi-check-circle-fill text-success me-2"></i>Contains numbers (0-9)</div>';
        } else {
            feedbackHTML += '<div class="requirement unmet"><i class="bi bi-circle me-2"></i>Contains numbers (0-9)</div>';
        }

        // Check for mixed case (bonus)
        if (requirements.mixed) {
            strength++;
        }

        feedbackHTML += '</div>';

        // Add strength indicator
        const strengthBar = createStrengthBar(strength);
        feedbackDiv.innerHTML = strengthBar + feedbackHTML;

        // Remove error class from password input
        if (strength >= 3) {
            passwordInput.classList.remove('is-invalid');
            passwordInput.classList.add('is-valid');
        } else if (password.length > 0) {
            passwordInput.classList.remove('is-valid');
            passwordInput.classList.add('is-invalid');
        } else {
            passwordInput.classList.remove('is-valid', 'is-invalid');
        }

        // Validate confirmation if it has a value
        if (passwordConfirmInput.value.length > 0) {
            validatePasswordMatch();
        }

        return strength >= 3;
    }

    /**
     * Validate password confirmation match
     */
    function validatePasswordMatch() {
        const feedbackDiv = document.getElementById('confirmFeedback');
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;

        if (passwordConfirm.length === 0) {
            feedbackDiv.innerHTML = '';
            passwordConfirmInput.classList.remove('is-valid', 'is-invalid');
            return true;
        }

        if (password === passwordConfirm) {
            feedbackDiv.innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle-fill me-2"></i>Passwords match</div>';
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.add('is-valid');
            return true;
        } else {
            feedbackDiv.innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-circle-fill me-2"></i>Passwords do not match</div>';
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmInput.classList.add('is-invalid');
            return false;
        }
    }

    /**
     * Create strength indicator bar
     */
    function createStrengthBar(strength) {
        let strengthText = '';
        let strengthColor = '';
        let strengthPercent = (strength / 4) * 100;

        if (strength === 0) {
            return '';
        } else if (strength === 1) {
            strengthText = 'Weak';
            strengthColor = '#dc3545'; // red
        } else if (strength === 2) {
            strengthText = 'Fair';
            strengthColor = '#ffc107'; // yellow
        } else if (strength === 3) {
            strengthText = 'Good';
            strengthColor = '#17a2b8'; // info
        } else if (strength >= 4) {
            strengthText = 'Strong';
            strengthColor = '#28a745'; // green
        }

        return `
            <div class="password-strength-bar mb-2">
                <div class="strength-label d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">Password Strength</small>
                    <small class="strength-text" style="color: ${strengthColor}; font-weight: 600;">${strengthText}</small>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: ${strengthPercent}%; background-color: ${strengthColor}; transition: all 0.3s ease;"
                         aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="4"></div>
                </div>
            </div>
        `;
    }

    /**
     * Validate password on form submission
     */
    function validatePasswordOnSubmit() {
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;

        // Validate password strength
        if (!validatePassword(password) || password.length < 8) {
            passwordInput.classList.add('is-invalid');
            showErrorAlert('Password must be at least 8 characters with letters and numbers');
            return false;
        }

        // Validate password match
        if (password !== passwordConfirm) {
            passwordConfirmInput.classList.add('is-invalid');
            showErrorAlert('Passwords do not match');
            return false;
        }

        return true;
    }

    /**
     * Show error alert
     */
    function showErrorAlert(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <strong>Password Error:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        const form = document.getElementById('addStaffForm');
        form.parentNode.insertBefore(alertDiv, form);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
