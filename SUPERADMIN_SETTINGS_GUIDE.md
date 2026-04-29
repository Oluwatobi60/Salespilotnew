# Superadmin Settings & Control Panel Documentation

## Overview

The Superadmin Settings & Control Panel provides a centralized, widget-based dashboard for managing all application configurations without touching source code. This system allows superadmins to dynamically control every aspect of the SalesPilot application.

---

## Table of Contents

1. [Features](#features)
2. [Access](#access)
3. [Settings Categories](#settings-categories)
4. [Quick Actions](#quick-actions)
5. [Using Settings in Code](#using-settings-in-code)
6. [API Reference](#api-reference)
7. [Migration Guide](#migration-guide)

---

## Features

### ✅ What You Can Control

- **General Settings**: App name, tagline, timezone, maintenance mode
- **Email Configuration**: SMTP settings for all application emails
- **Payment Gateways**: Paystack API keys, bank transfer details, currency
- **System Behavior**: Registration, email verification, session timeout, backups
- **Appearance**: Logo, favicon, brand colors, footer text
- **Security**: Password requirements, 2FA, IP whitelist, HTTPS enforcement

### 🎯 Benefits

- ✨ **No Code Changes Required**: Update settings through UI
- 🚀 **Instant Effect**: Changes apply immediately
- 🔄 **Cached for Performance**: Settings are cached for fast access
- 📝 **Audit Trail**: All changes are logged
- 🎨 **Widget-Based UI**: Organized, intuitive interface
- 🔒 **Secure**: Only superadmins can access

---

## Access

### Navigation

1. Log in as **Superadmin**
2. Navigate to **System → Settings** in the sidebar
3. The settings dashboard displays all configuration widgets

### URL

```
https://your-domain.com/superadmin/settings
```

---

## Settings Categories

### 1️⃣ General Settings Widget

**Icon**: Purple gradient gear icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Application Name | Text | Your app's name | SalesPilot |
| Application Tagline | Text | Short tagline | Smart Business Management |
| Support Email | Email | Customer support email | support@salespilot.com |
| Support Phone | Phone | Customer support phone | +234 800 000 0000 |
| Default Timezone | Text | Application timezone | Africa/Lagos |
| Maintenance Mode | Boolean | Put app in maintenance | Enabled/Disabled |
| Maintenance Message | Text | Message during maintenance | We're upgrading... |

**Impact**: Changes app name throughout the application, updates contact information

---

### 2️⃣ Email Settings Widget

**Icon**: Blue gradient envelope icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Mail Driver | Text | Email service | smtp |
| SMTP Host | Text | Mail server address | smtp.gmail.com |
| SMTP Port | Number | Mail server port | 587 |
| SMTP Username | Text | Auth username | noreply@salespilot.com |
| SMTP Password | Password | Auth password | ••••••••••••• |
| Mail Encryption | Text | Encryption type | tls |
| From Email Address | Email | Default sender email | noreply@salespilot.com |
| From Name | Text | Default sender name | SalesPilot |

**Impact**: Controls all email sent from the application

**Test Feature**: Click "Test Email" button to send a test email and verify configuration

---

### 3️⃣ Payment Settings Widget

**Icon**: Green gradient credit card icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Paystack Public Key | Text | Paystack public API key | pk_test_xxxxx |
| Paystack Secret Key | Password | Paystack secret key | sk_test_xxxxx |
| Enable Paystack | Boolean | Accept card payments | Enabled/Disabled |
| Enable Bank Transfer | Boolean | Accept bank transfers | Enabled/Disabled |
| Bank Name | Text | Your bank name | GTBank |
| Account Number | Text | Bank account number | 0123456789 |
| Account Name | Text | Account holder name | SalesPilot Technologies |
| Currency | Text | Currency code | NGN |
| Currency Symbol | Text | Currency symbol | ₦ |

**Impact**: Controls payment methods, Paystack integration, bank transfer details

---

### 4️⃣ System Settings Widget

**Icon**: Orange gradient CPU icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Enable Registration | Boolean | Allow new signups | Enabled/Disabled |
| Require Email Verification | Boolean | Force email verification | Enabled/Disabled |
| Default Trial Days | Number | Free trial period | 7 |
| Session Lifetime | Number | Session timeout (minutes) | 120 |
| Max Login Attempts | Number | Failed login threshold | 5 |
| Enable Auto Backup | Boolean | Automatic database backups | Enabled/Disabled |
| Backup Frequency | Text | Backup schedule | daily/weekly/monthly |

**Impact**: Controls user registration, authentication, and system maintenance

---

### 5️⃣ Appearance Settings Widget

**Icon**: Pink gradient palette icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Application Logo | File | Main logo (upload) | salespilot-logo.png |
| Favicon | File | Browser icon (upload) | favicon.ico |
| Primary Color | Color | Main brand color | #667eea |
| Secondary Color | Color | Secondary brand color | #764ba2 |
| Footer Text | Text | Footer copyright | © 2026 SalesPilot |

**Impact**: Changes branding, colors, and visual identity

**File Upload**: Accepts image files (PNG, JPG, SVG). Previous logo displayed with preview.

---

### 6️⃣ Security Settings Widget

**Icon**: Red gradient shield icon

| Setting | Type | Description | Example |
|---------|------|-------------|---------|
| Min Password Length | Number | Minimum password chars | 8 |
| Require Strong Password | Boolean | Enforce complexity rules | Enabled/Disabled |
| Enable 2FA | Boolean | Two-factor authentication | Enabled/Disabled |
| IP Whitelist | Text | Allowed IPs (comma-separated) | 192.168.1.1, 10.0.0.1 |
| Force HTTPS | Boolean | Redirect HTTP to HTTPS | Enabled/Disabled |

**Impact**: Enforces security policies across the application

---

## Quick Actions

Located at the top of the settings page for instant system management.

### 🔴 Enable/Disable Maintenance Mode

**Button**: Red "Enable Maintenance" / "Disable Maintenance"

**What it does**:
- Puts application offline for all users except superadmins
- Shows maintenance message to visitors
- Superadmins can access via `/superadmin-bypass` secret URL

**When to use**:
- During major updates
- Database migrations
- Server maintenance
- Emergency fixes

```
Access during maintenance: https://your-domain.com/superadmin-bypass
```

---

### 🔵 Clear All Cache

**Button**: Blue "Clear All Cache"

**What it does**:
- Clears application cache
- Clears configuration cache
- Clears route cache
- Clears view cache
- Clears settings cache

**When to use**:
- After changing settings
- After code deployments
- When settings don't update
- Performance issues

---

### 🟢 Backup Database

**Button**: Green "Backup Database"

**What it does**:
- Creates immediate database backup
- Stores backup files in `storage/app/backups/`
- Includes all tables and data

**When to use**:
- Before major changes
- Before updates
- Regular backups
- Pre-deployment

---

### 🔵 Test Email

**Button**: Blue "Test Email"

**What it does**:
- Opens modal to enter recipient email
- Sends test email using configured SMTP settings
- Verifies email configuration

**When to use**:
- After changing email settings
- Troubleshooting email issues
- Verifying SMTP credentials

---

## Using Settings in Code

### Helper Functions

The system provides convenient helper functions for accessing settings:

#### 1. Get Any Setting

```php
// Get setting value with default
$appName = setting('app_name', 'SalesPilot');
$supportEmail = setting('support_email');
```

#### 2. Get Settings by Group

```php
// Get all email settings
$emailSettings = settings('email');

// Get all settings
$allSettings = settings();
```

#### 3. Update Setting

```php
// Update a setting value
update_setting('app_name', 'My New App Name');
```

#### 4. Specialized Helpers

```php
// Application name
$name = app_name();

// Application logo URL
$logo = app_logo();

// Check maintenance mode
if (is_maintenance_mode()) {
    // Handle maintenance
}

// Support contact
$email = support_email();
$phone = support_phone();

// Brand colors
$primary = primary_color();
$secondary = secondary_color();

// Check registration status
if (is_registration_enabled()) {
    // Allow registration
}

// Currency
$symbol = currency_symbol(); // ₦
$code = currency_code(); // NGN
```

### In Blade Templates

```blade
{{-- Display app name --}}
<h1>{{ app_name() }}</h1>

{{-- Show support email --}}
<a href="mailto:{{ support_email() }}">Contact Support</a>

{{-- Display logo --}}
@if(app_logo())
    <img src="{{ app_logo() }}" alt="{{ app_name() }}">
@endif

{{-- Apply brand colors --}}
<div style="background: {{ primary_color() }};">
    ...
</div>

{{-- Check settings --}}
@if(is_registration_enabled())
    <a href="{{ route('register') }}">Sign Up</a>
@endif
```

### Direct Model Access

```php
use App\Models\AppSetting;

// Get setting
$value = AppSetting::get('app_name', 'Default');

// Set setting
AppSetting::set('app_name', 'New Name');

// Get by group
$emailSettings = AppSetting::getByGroup('email');

// Clear cache
AppSetting::clearCache();
```

---

## API Reference

### AppSetting Model

#### Static Methods

**`get(string $key, mixed $default = null): mixed`**
- Retrieve setting value by key
- Returns default if not found
- Cached for performance

**`set(string $key, mixed $value): bool`**
- Update setting value
- Clears cache automatically
- Returns success status

**`getByGroup(string $group): Collection`**
- Get all settings in a group
- Returns collection of settings

**`getAllGrouped(): array`**
- Get all settings grouped by category
- Returns associative array

**`clearCache(): void`**
- Clear all settings cache
- Forces reload from database

---

### SettingsController Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/superadmin/settings` | GET | Display settings dashboard |
| `/superadmin/settings` | PUT | Update all settings |
| `/superadmin/settings/test-email` | POST | Send test email |
| `/superadmin/settings/clear-cache` | POST | Clear application cache |
| `/superadmin/settings/run-backup` | POST | Run database backup |
| `/superadmin/settings/toggle-maintenance` | POST | Toggle maintenance mode |

---

## Migration Guide

### Setup Instructions

1. **Run Migration**

```bash
cd /path/to/salespilot
php artisan migrate
```

This creates the `app_settings` table and populates default settings.

2. **Regenerate Autoloader**

```bash
composer dump-autoload
```

This loads the settings helper functions.

3. **Access Settings Panel**

- Log in as superadmin
- Navigate to System → Settings
- Configure your settings

4. **Clear Cache** (if needed)

```bash
php artisan config:clear
php artisan cache:clear
```

---

### Database Structure

**Table**: `app_settings`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| key | string | Unique setting identifier |
| value | text | Setting value |
| type | string | Data type (text, number, boolean, file, color) |
| group | string | Category (general, email, payment, etc.) |
| label | string | Display name |
| description | text | Help text |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

---

## Best Practices

### ✅ Do's

1. **Always use helper functions** instead of direct database queries
2. **Clear cache** after bulk setting changes
3. **Test email settings** before relying on them
4. **Backup database** before major changes
5. **Use maintenance mode** during deployments
6. **Document custom settings** if you add new ones

### ❌ Don'ts

1. Don't hardcode values that should be configurable
2. Don't modify settings directly in database
3. Don't forget to clear cache after changes
4. Don't enable maintenance without notifying users
5. Don't store sensitive data without encryption

---

## Troubleshooting

### Settings Not Updating

**Problem**: Changes don't reflect immediately

**Solution**:
```bash
php artisan config:clear
php artisan cache:clear
```
Or use "Clear All Cache" button in Quick Actions

### Email Not Sending

**Problem**: Test emails fail

**Solutions**:
1. Verify SMTP credentials
2. Check port (587 for TLS, 465 for SSL)
3. Enable "Less secure apps" for Gmail
4. Check firewall/server settings
5. Review error logs: `storage/logs/laravel.log`

### Maintenance Mode Issues

**Problem**: Can't access during maintenance

**Solution**:
- Use bypass URL: `https://your-domain.com/superadmin-bypass`
- Or disable via command line:
```bash
php artisan up
```

### File Upload Fails

**Problem**: Logo/favicon upload fails

**Solutions**:
1. Check storage permissions: `chmod -R 775 storage`
2. Create symbolic link: `php artisan storage:link`
3. Check file size limits in `php.ini`
4. Verify file type is allowed (image/*)

---

## Security Considerations

### Access Control

- Settings page requires `auth:superadmin` middleware
- Only superadmins can view/modify settings
- All actions are logged

### Data Protection

- Passwords stored as password type (masked input)
- API keys hidden in UI
- Cache cleared on updates
- Settings validated before saving

### Maintenance Mode

- Superadmin bypass secret: `superadmin-bypass`
- Change in `.env` for production:
```env
APP_MAINTENANCE_SECRET=your-custom-secret
```

---

## Examples

### Example 1: Dynamic Branding

```blade
<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ app_name() }} - {{ setting('app_tagline') }}</title>
    <link rel="icon" href="{{ app_logo() }}">
    <style>
        :root {
            --primary: {{ primary_color() }};
            --secondary: {{ secondary_color() }};
        }
    </style>
</head>
<body>
    <header style="background: var(--primary);">
        <img src="{{ app_logo() }}" alt="{{ app_name() }}">
        <h1>{{ app_name() }}</h1>
    </header>
    <footer>
        <p>{{ setting('footer_text') }}</p>
        <p>Contact: {{ support_email() }} | {{ support_phone() }}</p>
    </footer>
</body>
</html>
```

### Example 2: Conditional Registration

```php
// app/Http/Controllers/Auth/RegisterController.php
public function showRegistrationForm()
{
    if (!is_registration_enabled()) {
        abort(403, 'Registration is currently disabled.');
    }
    
    return view('auth.register');
}
```

### Example 3: Payment Configuration

```php
// app/Http/Controllers/PaymentController.php
public function processPayment(Request $request)
{
    if (setting('paystack_enabled', true)) {
        $paystackKey = setting('paystack_public_key');
        // Process with Paystack
    } elseif (setting('bank_transfer_enabled', true)) {
        $bankDetails = [
            'bank_name' => setting('bank_name'),
            'account_number' => setting('bank_account_number'),
            'account_name' => setting('bank_account_name'),
        ];
        // Show bank transfer details
    }
}
```

---

## Adding Custom Settings

### Step 1: Add to Migration

Edit `database/migrations/2026_04_29_100000_create_app_settings_table.php`:

```php
// Add to $defaultSettings array
[
    'key' => 'my_custom_setting',
    'value' => 'default_value',
    'type' => 'text',
    'group' => 'general',
    'label' => 'My Custom Setting',
    'description' => 'Description of what this does',
],
```

### Step 2: Create Helper (Optional)

Edit `app/Helpers/SettingsHelper.php`:

```php
if (!function_exists('my_custom_setting')) {
    function my_custom_setting(): string
    {
        return setting('my_custom_setting', 'default');
    }
}
```

### Step 3: Run Migration

```bash
php artisan migrate:fresh --seed
# OR manually insert via SQL
```

### Step 4: Use It

```php
$value = setting('my_custom_setting');
// OR
$value = my_custom_setting();
```

---

## Support

For issues or questions:
- **Email**: {{ support_email() }}
- **Phone**: {{ support_phone() }}
- **Documentation**: This file
- **Logs**: `storage/logs/laravel.log`

---

## Changelog

### Version 1.0 (April 29, 2026)

- ✅ Initial release
- ✅ 6 setting categories (40+ settings)
- ✅ Widget-based UI
- ✅ Quick actions (maintenance, cache, backup, test email)
- ✅ Helper functions
- ✅ Caching system
- ✅ File upload support
- ✅ Comprehensive documentation

---

**Built with ❤️ for SalesPilot**
