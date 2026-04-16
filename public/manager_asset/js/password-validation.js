/**
 * Password Validation Component Usage
 * Uses the PasswordValidator component for reusable validation across different forms
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize password validator for Add Staff Form
    if (document.getElementById('addStaffForm')) {
        new PasswordValidator({
            passwordSelector: '#password',
            confirmSelector: '#password_confirmation',
            formSelector: '#addStaffForm',
            minLength: 8,
            showToggle: true,
            requiredConfirm: true
        });
    }

    // Initialize password validator for Edit Staff Form
    if (document.getElementById('editStaffForm')) {
        new PasswordValidator({
            passwordSelector: '#password',
            confirmSelector: '#password_confirmation',
            formSelector: '#editStaffForm',
            minLength: 8,
            showToggle: true,
            requiredConfirm: false // Optional for edit forms
        });
    }

    // Initialize password validator for Change Password Form
    if (document.getElementById('changePasswordForm')) {
        new PasswordValidator({
            passwordSelector: '#newPassword',
            confirmSelector: '#confirmPassword',
            formSelector: '#changePasswordForm',
            minLength: 8,
            showToggle: true,
            requiredConfirm: true
        });
    }

    // Initialize password validator for BRM Create Form
    if (document.getElementById('createBrmForm')) {
        new PasswordValidator({
            passwordSelector: 'input[name="password"]',
            confirmSelector: 'input[name="password_confirmation"]',
            formSelector: '#createBrmForm',
            minLength: 8,
            showToggle: true,
            requiredConfirm: true
        });
    }

    // Initialize password validator for BRM Edit Form
    if (document.querySelector('form[action*="brms"]')) {
        new PasswordValidator({
            passwordSelector: 'input[name="password"]',
            confirmSelector: 'input[name="password_confirmation"]',
            minLength: 8,
            showToggle: true,
            requiredConfirm: false // Optional for edit forms
        });
    }
});
