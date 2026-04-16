# Password Validator Component - Usage Guide

A reusable, configurable password validation component for Laravel Blade templates.

## Features

- ✅ Real-time password strength validation
- ✅ Password confirmation matching
- ✅ Show/hide password toggle
- ✅ Customizable minimum length
- ✅ Progressive feedback with visual indicators
- ✅ Form submission validation
- ✅ Auto-dismissing error alerts
- ✅ Works with any form selector

## Files

- `password-validator.js` - The reusable PasswordValidator class
- `password-validation.js` - Usage examples and initialization
- `password-validation.css` - Styling for validation feedback

## Installation

Include both files in your template:

```blade
<link rel="stylesheet" href="{{ asset('manager_asset/css/password-validation.css') }}">
<script src="{{ asset('manager_asset/js/password-validator.js') }}"></script>
<script src="{{ asset('manager_asset/js/password-validation.js') }}"></script>
```

## Usage

### Basic Usage

Simply use the standard password input fields in your form:

```blade
<form id="myForm" method="POST" action="/update-password">
    @csrf
    
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Save</button>
</form>
```

The `password-validation.js` file will automatically initialize validators for common form IDs.

### Custom Initialization

For custom forms, initialize the validator in your JavaScript:

```javascript
new PasswordValidator({
    passwordSelector: '#password',           // Password input selector
    confirmSelector: '#password_confirmation', // Confirm password selector
    formSelector: '#myForm',                 // Form selector (optional)
    minLength: 8,                            // Minimum password length (default: 8)
    showToggle: true,                        // Show/hide toggle button (default: true)
    requiredConfirm: true,                   // Confirmation field required (default: true)
    onSubmit: function() {}                  // Optional callback on valid submission
});
```

### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `passwordSelector` | string | `#password` | CSS selector for password input |
| `confirmSelector` | string | `#password_confirmation` | CSS selector for confirmation input |
| `formSelector` | string | `null` | CSS selector for form element |
| `minLength` | number | `8` | Minimum password length requirement |
| `showToggle` | boolean | `true` | Display show/hide password toggle buttons |
| `requiredConfirm` | boolean | `true` | Make confirmation field required |
| `onSubmit` | function | `null` | Callback function on successful submission |

## Methods

### isValid()
```javascript
const validator = new PasswordValidator({...});
if (validator.isValid()) {
    // Password is valid
}
```

### reset()
```javascript
const validator = new PasswordValidator({...});
validator.reset(); // Clear all fields and validation states
```

## Examples

### Example 1: Add User Form

```blade
<form id="addUserForm" method="POST" action="{{ route('users.store') }}">
    @csrf
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Create User</button>
</form>

<script>
    new PasswordValidator({
        passwordSelector: '#password',
        confirmSelector: '#password_confirmation',
        formSelector: '#addUserForm',
        minLength: 8
    });
</script>
```

### Example 2: Change Password (No Confirmation Required)

```blade
<form id="changePasswordForm" method="POST" action="{{ route('password.update') }}">
    @csrf
    
    <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" id="newPassword" class="form-control" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Update Password</button>
</form>

<script>
    new PasswordValidator({
        passwordSelector: '#newPassword',
        confirmSelector: 'input[name="confirm_password"]', // Optional field
        formSelector: '#changePasswordForm',
        requiredConfirm: false // Make confirmation optional
    });
</script>
```

### Example 3: Edit User (Password Optional)

```blade
<form id="editUserForm" method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">New Password (Optional)</label>
        <input type="password" name="password" id="password" class="form-control">
    </div>
    
    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
    </div>
    
    <button type="submit" class="btn btn-primary">Update User</button>
</form>

<script>
    new PasswordValidator({
        passwordSelector: '#password',
        confirmSelector: '#password_confirmation',
        formSelector: '#editUserForm',
        requiredConfirm: false // Confirmation is optional when password is empty
    });
</script>
```

## Validation Rules

The validator enforces the following password requirements:

1. **Minimum Length**: At least 8 characters (configurable)
2. **Letters**: Must contain at least one letter (a-z, A-Z)
3. **Numbers**: Must contain at least one number (0-9)
4. **No Spaces**: Cannot contain spaces
5. **Match**: Confirmation must match the password

## Visual Feedback

The component provides:

- Real-time validation feedback with checkmarks (✓) and circles (○)
- Color-coded strength indicator (Red → Orange → Yellow → Green)
- Strength levels: Weak → Fair → Good → Strong → Very Strong
- Auto-dismissing error alerts on form submission
- Input field styling (green border = valid, red border = invalid)

## Styling

To customize the appearance, modify `password-validation.css`:

```css
/* Override password requirements color */
.password-requirements .requirement.met {
    color: #28a745; /* Green for completed requirements */
}

/* Override strength bar colors */
.progress-bar {
    background-color: #28a745; /* Strong password color */
}
```

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Notes

- The component automatically handles toggle buttons creation
- Validation feedback divs are created dynamically if not present
- Multiple validators can be initialized on the same page
- Component is framework-agnostic and can be used with any form
