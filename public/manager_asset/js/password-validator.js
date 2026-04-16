/**
 * Reusable Password Validation Component
 * Can be used with any form by passing configuration options
 *
 * Usage:
 *   const validator = new PasswordValidator({
 *       passwordSelector: '#password',
 *       confirmSelector: '#password_confirmation',
 *       formSelector: '#myForm',
 *       minLength: 8
 *   });
 */

class PasswordValidator {
    constructor(config = {}) {
        this.config = {
            passwordSelector: config.passwordSelector || '#password',
            confirmSelector: config.confirmSelector || '#password_confirmation',
            formSelector: config.formSelector || null,
            minLength: config.minLength || 8,
            showToggle: config.showToggle !== false, // Default true
            requiredConfirm: config.requiredConfirm !== false, // Default true
            onSubmit: config.onSubmit || null, // Optional callback
            ...config
        };

        this.passwordInput = document.querySelector(this.config.passwordSelector);
        this.passwordConfirmInput = document.querySelector(this.config.confirmSelector);
        this.form = this.config.formSelector ? document.querySelector(this.config.formSelector) : null;

        if (!this.passwordInput) {
            console.warn(`PasswordValidator: Password input not found with selector: ${this.config.passwordSelector}`);
            return;
        }

        this.init();
    }

    /**
     * Initialize the password validator
     */
    init() {
        // Create feedback divs if they don't exist
        this.createFeedbackElements();

        // Add toggle buttons if enabled
        if (this.config.showToggle) {
            this.createPasswordToggle(this.passwordInput, 'toggle-password');
            if (this.passwordConfirmInput) {
                this.createPasswordToggle(this.passwordConfirmInput, 'toggle-password-confirm');
            }
        }

        // Add event listeners
        this.passwordInput.addEventListener('input', () => this.validatePassword(this.passwordInput.value));

        if (this.passwordConfirmInput) {
            this.passwordConfirmInput.addEventListener('input', () => this.validatePasswordMatch());
        }

        // Form submission validation
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    /**
     * Create feedback div elements if they don't exist
     */
    createFeedbackElements() {
        if (!this.passwordInput.parentNode.querySelector('.password-feedback-main')) {
            const feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'password-feedback password-feedback-main mt-2';
            this.passwordInput.parentNode.appendChild(feedbackDiv);
        }

        if (this.passwordConfirmInput && !this.passwordConfirmInput.parentNode.querySelector('.password-feedback-confirm')) {
            const feedbackDiv = document.createElement('div');
            feedbackDiv.className = 'password-feedback password-feedback-confirm mt-2';
            this.passwordConfirmInput.parentNode.appendChild(feedbackDiv);
        }
    }

    /**
     * Create a toggle button for showing/hiding password
     */
    createPasswordToggle(inputElement, buttonId) {
        const parentContainer = inputElement.parentNode;
        const toggleBtn = document.createElement('button');

        toggleBtn.id = buttonId;
        toggleBtn.type = 'button';
        toggleBtn.className = 'password-toggle-btn';
        toggleBtn.title = 'Show Password';
        toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
        toggleBtn.style.cssText = 'position: absolute; right: 0.375rem; top: 50%; transform: translateY(-50%); border: none; background: none; color: #6c757d; padding: 0.25rem 0.5rem; margin: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 0.25rem;';

        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            this.togglePasswordVisibility(inputElement, toggleBtn);
        });

        toggleBtn.addEventListener('mousedown', (e) => {
            e.preventDefault();
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
    togglePasswordVisibility(inputElement, toggleBtn) {
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
    validatePassword(password) {
        const feedbackDiv = this.passwordInput.parentNode.querySelector('.password-feedback-main');
        const requirements = {
            minLength: password.length >= this.config.minLength,
            hasLetters: /[a-zA-Z]/.test(password),
            hasNumbers: /\d/.test(password),
            noSpaces: !/\s/.test(password),
            mixed: /[a-z]/.test(password) && /[A-Z]/.test(password)
        };

        let strength = 0;
        let feedbackHTML = '<div class="password-requirements">';

        if (password.length === 0) {
            feedbackDiv.innerHTML = '';
            this.passwordInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }

        // Check minimum length
        if (requirements.minLength) {
            strength++;
            feedbackHTML += `<div class="requirement met"><i class="bi bi-check-circle-fill text-success me-2"></i>At least ${this.config.minLength} characters</div>`;
        } else {
            feedbackHTML += `<div class="requirement unmet"><i class="bi bi-circle me-2"></i>At least ${this.config.minLength} characters</div>`;
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

        // Check for no spaces
        if (requirements.noSpaces) {
            strength++;
            feedbackHTML += '<div class="requirement met"><i class="bi bi-check-circle-fill text-success me-2"></i>No spaces</div>';
        } else {
            feedbackHTML += '<div class="requirement unmet"><i class="bi bi-circle me-2"></i>No spaces</div>';
        }

        // Check for mixed case (bonus)
        if (requirements.mixed) {
            strength++;
        }

        feedbackHTML += '</div>';

        const strengthBar = this.createStrengthBar(strength);
        feedbackDiv.innerHTML = strengthBar + feedbackHTML;

        // Update input validation classes
        if (strength >= 3) {
            this.passwordInput.classList.remove('is-invalid');
            this.passwordInput.classList.add('is-valid');
        } else if (password.length > 0) {
            this.passwordInput.classList.remove('is-valid');
            this.passwordInput.classList.add('is-invalid');
        } else {
            this.passwordInput.classList.remove('is-valid', 'is-invalid');
        }

        // Validate confirmation if it has a value
        if (this.passwordConfirmInput && this.passwordConfirmInput.value.length > 0) {
            this.validatePasswordMatch();
        }

        return strength >= 3;
    }

    /**
     * Validate password confirmation match
     */
    validatePasswordMatch() {
        if (!this.passwordConfirmInput) return true;

        const feedbackDiv = this.passwordConfirmInput.parentNode.querySelector('.password-feedback-confirm');
        const password = this.passwordInput.value;
        const passwordConfirm = this.passwordConfirmInput.value;

        if (passwordConfirm.length === 0) {
            feedbackDiv.innerHTML = '';
            this.passwordConfirmInput.classList.remove('is-valid', 'is-invalid');
            return true;
        }

        if (password === passwordConfirm) {
            feedbackDiv.innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle-fill me-2"></i>Passwords match</div>';
            this.passwordConfirmInput.classList.remove('is-invalid');
            this.passwordConfirmInput.classList.add('is-valid');
            return true;
        } else {
            feedbackDiv.innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-circle-fill me-2"></i>Passwords do not match</div>';
            this.passwordConfirmInput.classList.remove('is-valid');
            this.passwordConfirmInput.classList.add('is-invalid');
            return false;
        }
    }

    /**
     * Create strength indicator bar
     */
    createStrengthBar(strength) {
        let strengthText = '';
        let strengthColor = '';
        let strengthPercent = 0;

        if (strength === 0) {
            strengthText = 'None';
            strengthColor = '#dc3545';
            strengthPercent = 0;
        } else if (strength === 1) {
            strengthText = 'Very Weak';
            strengthColor = '#dc3545';
            strengthPercent = 20;
        } else if (strength === 2) {
            strengthText = 'Weak';
            strengthColor = '#fd7e14';
            strengthPercent = 40;
        } else if (strength === 3) {
            strengthText = 'Good';
            strengthColor = '#ffc107';
            strengthPercent = 60;
        } else if (strength === 4) {
            strengthText = 'Strong';
            strengthColor = '#28a745';
            strengthPercent = 80;
        } else {
            strengthText = 'Very Strong';
            strengthColor = '#20c997';
            strengthPercent = 100;
        }

        return `
            <div class="password-strength-bar">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">Password Strength</small>
                    <small class="strength-text" style="color: ${strengthColor}; font-weight: 600;">${strengthText}</small>
                </div>
                <div class="progress" style="height: 6px;">
                    <div class="progress-bar" role="progressbar"
                         style="width: ${strengthPercent}%; background-color: ${strengthColor}; transition: all 0.3s ease;"
                         aria-valuenow="${strength}" aria-valuemin="0" aria-valuemax="5"></div>
                </div>
            </div>
        `;
    }

    /**
     * Handle form submission validation
     */
    handleFormSubmit(e) {
        if (!this.validatePasswordOnSubmit()) {
            e.preventDefault();
            return false;
        }

        // Call optional callback
        if (this.config.onSubmit) {
            this.config.onSubmit();
        }

        return true;
    }

    /**
     * Validate password on form submission
     */
    validatePasswordOnSubmit() {
        const password = this.passwordInput.value;
        const passwordConfirm = this.passwordConfirmInput ? this.passwordConfirmInput.value : '';

        // Validate password strength
        if (!this.validatePassword(password) || password.length < this.config.minLength) {
            this.passwordInput.classList.add('is-invalid');
            this.showErrorAlert(`Password must be at least ${this.config.minLength} characters with letters and numbers`);
            return false;
        }

        // Validate password match if confirmation field is required
        if (this.config.requiredConfirm && this.passwordConfirmInput) {
            if (password !== passwordConfirm) {
                this.passwordConfirmInput.classList.add('is-invalid');
                this.showErrorAlert('Passwords do not match');
                return false;
            }
        }

        return true;
    }

    /**
     * Show error alert
     */
    showErrorAlert(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <strong>Password Error:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        if (this.form) {
            this.form.parentNode.insertBefore(alertDiv, this.form);
        } else {
            this.passwordInput.parentNode.insertBefore(alertDiv, this.passwordInput);
        }

        // Auto-remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    /**
     * Get validation state
     */
    isValid() {
        return this.validatePasswordOnSubmit();
    }

    /**
     * Reset validation
     */
    reset() {
        this.passwordInput.value = '';
        if (this.passwordConfirmInput) {
            this.passwordConfirmInput.value = '';
        }
        this.passwordInput.classList.remove('is-valid', 'is-invalid');
        if (this.passwordConfirmInput) {
            this.passwordConfirmInput.classList.remove('is-valid', 'is-invalid');
        }
        const feedbackMain = this.passwordInput.parentNode.querySelector('.password-feedback-main');
        const feedbackConfirm = this.passwordConfirmInput?.parentNode.querySelector('.password-feedback-confirm');
        if (feedbackMain) feedbackMain.innerHTML = '';
        if (feedbackConfirm) feedbackConfirm.innerHTML = '';
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PasswordValidator;
}
