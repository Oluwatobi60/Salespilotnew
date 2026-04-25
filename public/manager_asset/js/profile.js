
// Edit Profile Panel
const openEditPanelBtn = document.getElementById('openEditPanel');
const editPanel = document.getElementById('editPanel');
const editPanelOverlay = document.getElementById('editPanelOverlay');
const closeEditPanelBtn = document.getElementById('closeEditPanelBtn');
const cancelEditPanelBtn = document.getElementById('cancelEditPanelBtn');

if (openEditPanelBtn && editPanel) {
    openEditPanelBtn.addEventListener('click', () => {
        editPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}
if (closeEditPanelBtn && editPanel) {
    closeEditPanelBtn.addEventListener('click', () => {
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (editPanelOverlay && editPanel) {
    editPanelOverlay.addEventListener('click', () => {
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (cancelEditPanelBtn && editPanel) {
    cancelEditPanelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}

// Change Password Panel
const openPasswordPanelBtn = document.getElementById('openPasswordPanel');
const passwordPanel = document.getElementById('passwordPanel');
const panelOverlay = document.getElementById('panelOverlay');
const closePanelBtn = document.getElementById('closePanelBtn');
const cancelPasswordPanelBtn = document.getElementById('cancelPasswordPanelBtn');

if (openPasswordPanelBtn && passwordPanel) {
    openPasswordPanelBtn.addEventListener('click', () => {
        passwordPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}
if (closePanelBtn && passwordPanel) {
    closePanelBtn.addEventListener('click', () => {
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (panelOverlay && passwordPanel) {
    panelOverlay.addEventListener('click', () => {
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (cancelPasswordPanelBtn && passwordPanel) {
    cancelPasswordPanelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
// Toggle password visibility
['CurrentPassword', 'NewPassword', 'ConfirmPassword'].forEach(function(type) {
    const toggleBtn = document.getElementById('toggle' + type);
    const input = document.getElementById(type.charAt(0).toLowerCase() + type.slice(1));
    if (toggleBtn && input) {
        toggleBtn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.querySelector('i').classList.remove('bi-eye');
                toggleBtn.querySelector('i').classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                toggleBtn.querySelector('i').classList.remove('bi-eye-slash');
                toggleBtn.querySelector('i').classList.add('bi-eye');
            }
        });
    }
});




// for change password form
// Toggle password visibility

    // Toggle password visibility
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '_icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    // Check password strength
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');

        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);

        // Update requirement indicators
        updateRequirement('req-length', hasLength);
        updateRequirement('req-uppercase', hasUpper);
        updateRequirement('req-lowercase', hasLower);
        updateRequirement('req-number', hasNumber);

        // Calculate strength
        let strength = 0;
        if (hasLength) strength++;
        if (hasUpper) strength++;
        if (hasLower) strength++;
        if (hasNumber) strength++;
        if (password.length >= 12) strength++;
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        // Update strength bar
        strengthBar.className = 'password-strength-progress';
        if (strength <= 2) {
            strengthBar.classList.add('weak');
            strengthText.innerHTML = 'Password strength: <span style="color: #f56565;">Weak</span>';
        } else if (strength <= 4) {
            strengthBar.classList.add('medium');
            strengthText.innerHTML = 'Password strength: <span style="color: #ed8936;">Medium</span>';
        } else {
            strengthBar.classList.add('strong');
            strengthText.innerHTML = 'Password strength: <span style="color: #48bb78;">Strong</span>';
        }

        // Check password match if confirm field has value
        checkPasswordMatch();
    }

    // Update requirement indicator
    function updateRequirement(id, met) {
        const element = document.getElementById(id);
        if (met) {
            element.classList.add('met');
        } else {
            element.classList.remove('met');
        }
    }

    // Check if passwords match
    function checkPasswordMatch() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        const matchText = document.getElementById('passwordMatchText');

        if (confirmPassword.length === 0) {
            matchText.textContent = '';
            matchText.className = 'password-match-text';
            return;
        }

        if (newPassword === confirmPassword) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'password-match-text match';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'password-match-text no-match';
        }
    }
