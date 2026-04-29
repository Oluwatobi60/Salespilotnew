# Loading Button Component

A reusable button component that shows a loading state with a spinner when clicked.

## Features

- ✅ Automatic loading state on form submission
- ✅ Spinning circular progress indicator
- ✅ Customizable loading text
- ✅ Works with any form or button
- ✅ Automatic initialization
- ✅ Prevents double submissions
- ✅ Responsive and accessible

## Installation

### 1. Include CSS and JavaScript files

```blade
<!-- In your blade template head -->
<link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">

<!-- Before closing body tag -->
<script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>
```

## Usage

### Basic Usage (Automatic)

Simply add the `btn-loading` class to your submit button and wrap the text in a `btn-text` span:

```html
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <!-- Your form fields here -->
    
    <button type="submit" class="btn-primary-custom btn-loading">
        <span class="btn-text">Sign In</span>
        <span class="btn-spinner"></span>
    </button>
</form>
```

That's it! The component will automatically:
- Show the spinner when the form is submitted
- Disable the button to prevent double submissions
- Re-enable the button if the user navigates back

### Custom Loading Text

Add `data-loading-text` attribute to customize the text shown while loading:

```html
<button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Logging in...">
    <span class="btn-text">Sign In</span>
    <span class="btn-spinner"></span>
</button>
```

### Auto-Stop After Timeout

Add `data-loading-timeout` to automatically stop the loading state after X milliseconds:

```html
<button type="submit" class="btn-loading" data-loading-timeout="5000">
    <span class="btn-text">Submit</span>
    <span class="btn-spinner"></span>
</button>
```

### Manual Control

You can also manually control the loading state using JavaScript:

```javascript
const btn = document.querySelector('.btn-loading');

// Start loading
LoadingButton.start(btn);

// Start with custom text
LoadingButton.start(btn, 'Please wait...');

// Stop loading
LoadingButton.stop(btn);

// Toggle loading state
LoadingButton.toggle(btn);
```

## Examples

### Example 1: Login Form

```blade
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" required>
    </div>
    
    <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required>
    </div>
    
    <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Signing in...">
        <span class="btn-text">Sign In</span>
        <span class="btn-spinner"></span>
    </button>
</form>
```

### Example 2: Registration Form

```blade
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <!-- Your form fields -->
    
    <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Creating account...">
        <span class="btn-text">Create Account</span>
        <span class="btn-spinner"></span>
    </button>
</form>
```

### Example 3: Payment Form

```blade
<form method="POST" action="{{ route('payment.process') }}" id="paymentForm">
    @csrf
    
    <!-- Payment fields -->
    
    <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Processing payment...">
        <span class="btn-text">Pay Now</span>
        <span class="btn-spinner"></span>
    </button>
</form>
```

### Example 4: AJAX Form (Manual Control)

```html
<button type="button" class="btn-loading" id="saveBtn">
    <span class="btn-text">Save Changes</span>
    <span class="btn-spinner"></span>
</button>

<script>
document.getElementById('saveBtn').addEventListener('click', function() {
    const btn = this;
    
    // Start loading
    LoadingButton.start(btn, 'Saving...');
    
    // Your AJAX call
    fetch('/api/save', {
        method: 'POST',
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        // Stop loading on success
        LoadingButton.stop(btn);
        alert('Saved successfully!');
    })
    .catch(error => {
        // Stop loading on error
        LoadingButton.stop(btn);
        alert('Error saving!');
    });
});
</script>
```

## Styling

The component works with any button style. Just add the `btn-loading` class:

```html
<!-- Works with any button class -->
<button class="btn-primary-custom btn-loading">...</button>
<button class="btn-outline-custom btn-loading">...</button>
<button class="btn-success btn-loading">...</button>
```

## Button Sizes

Add size classes for different spinner sizes:

```html
<!-- Small button -->
<button class="btn-sm btn-loading">
    <span class="btn-text">Submit</span>
    <span class="btn-spinner"></span>
</button>

<!-- Large button -->
<button class="btn-lg btn-loading">
    <span class="btn-text">Submit</span>
    <span class="btn-spinner"></span>
</button>
```

## API Reference

### Methods

#### `LoadingButton.init()`
Automatically initializes all forms with loading buttons. Called automatically on page load.

#### `LoadingButton.start(button, loadingText)`
- **button**: HTMLElement - The button to start loading
- **loadingText**: string (optional) - Custom loading text

Start the loading state for a button.

#### `LoadingButton.stop(button)`
- **button**: HTMLElement - The button to stop loading

Stop the loading state and restore the original button text.

#### `LoadingButton.toggle(button)`
- **button**: HTMLElement - The button to toggle

Toggle between loading and normal state.

#### `LoadingButton.create(options)`
- **options**: Object - Button configuration

Create a new loading button element programmatically.

```javascript
const button = LoadingButton.create({
    text: 'Submit',
    loadingText: 'Processing...',
    type: 'submit',
    className: 'btn-primary-custom btn-loading',
    icon: 'uil uil-check'
});
document.querySelector('form').appendChild(button);
```

## Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Notes

- The component automatically prevents double submissions by disabling the button
- Works with browser back button (restores button state)
- Compatible with form validation (only activates if form is valid)
- No dependencies required (vanilla JavaScript)
