# System Preferences - Global Application Settings

## Overview
The System Preferences feature allows the Superadmin to dynamically configure system-wide settings that automatically apply throughout the entire application. Changes made in the Superadmin panel are immediately reflected across all modules.

## Accessing System Preferences
1. Login as **Superadmin**
2. Navigate to **Settings > System Preferences** or click **System Preferences** in the sidebar
3. Click on the **Global Preferences** tab
4. Modify any settings and click **Save Changes**

## Available Settings

### 1. Regional Settings

#### Default Currency
- **Options**: NGN, USD, EUR, GBP
- **Default**: NGN (Nigerian Naira)
- **Impact**: Sets the currency code used throughout the application
- **Usage in Code**: 
  ```php
  default_currency()  // Returns: 'NGN'
  @currency          // Blade directive
  ```

#### Default Timezone
- **Options**: Africa/Lagos, UTC, America/New_York, Europe/London
- **Default**: Africa/Lagos
- **Impact**: Sets the timezone for all date/time operations, timestamps, and displays
- **Usage in Code**:
  ```php
  default_timezone()  // Returns: 'Africa/Lagos'
  ```

### 2. Date & Time Format

#### Date Format
- **Options**: 
  - YYYY-MM-DD (Y-m-d)
  - DD/MM/YYYY (d/m/Y)
  - MM/DD/YYYY (m/d/Y)
- **Default**: YYYY-MM-DD
- **Impact**: Controls how dates are displayed across the application
- **Usage in Code**:
  ```php
  system_date_format()              // Returns: 'Y-m-d'
  format_date($date)         // Formats date according to setting
  @formatDate($model->created_at)  // Blade directive
  ```

#### Time Format
- **Options**:
  - 24 Hour (H:i:s)
  - 12 Hour (h:i A)
- **Default**: 24 Hour
- **Impact**: Controls how times are displayed
- **Usage in Code**:
  ```php
  system_time_format()              // Returns: 'H:i:s'
  format_time($time)         // Formats time according to setting
  @formatTime($model->created_at)  // Blade directive
  ```

### 3. System Limits

#### Items Per Page
- **Range**: 5-100
- **Default**: 10
- **Impact**: Sets default pagination for tables and lists throughout the application
- **Usage in Code**:
  ```php
  items_per_page()  // Returns: 10
  
  // In Controllers:
  $users->paginate(items_per_page());
  ```

#### Session Timeout
- **Range**: 5-1440 minutes
- **Default**: 120 minutes (2 hours)
- **Impact**: Controls how long users stay logged in without activity
- **Applied**: Automatically to Laravel session configuration

#### Max Upload Size
- **Range**: 1024-10240 KB (1-10 MB)
- **Default**: 2048 KB (2 MB)
- **Impact**: Maximum file size allowed for uploads
- **Usage in Code**:
  ```php
  max_upload_size()     // Returns: 2048 (KB)
  max_upload_size_mb()  // Returns: 2.0 (MB)
  ```

#### Allowed File Types
- **Format**: Comma-separated file extensions
- **Default**: jpg,jpeg,png,pdf
- **Impact**: File types permitted for upload
- **Usage in Code**:
  ```php
  allowed_file_types()           // Returns: ['jpg', 'jpeg', 'png', 'pdf']
  is_allowed_file('photo.jpg')   // Returns: true/false
  ```

## How It Works

### 1. Settings Storage
All settings are stored in the `app_settings` database table with the following structure:
- `key`: Setting identifier (e.g., 'default_currency')
- `value`: Current value
- `type`: Data type (text, number, boolean, etc.)
- `group`: Setting category ('system', 'email', 'payment', etc.)
- `label`: Display name
- `description`: Setting description

### 2. Application Flow

```
┌─────────────────────────────────────────┐
│  Superadmin Updates System Preferences  │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│  Settings Saved to Database             │
│  (app_settings table)                   │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│  Cache Cleared for Updated Settings     │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│  AppServiceProvider Applies Settings    │
│  - Sets timezone globally               │
│  - Updates session config               │
│  - Shares variables with views          │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│  ApplySystemPreferences Middleware      │
│  - Applied to all web requests          │
│  - Enforces timezone and upload limits  │
└────────────────┬────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│  Settings Available Throughout App      │
│  - Helper functions                     │
│  - Blade directives                     │
│  - View variables                       │
└─────────────────────────────────────────┘
```

### 3. Global Application

#### In Controllers
```php
use App\Models\User;

// Use dynamic pagination
$users = User::paginate(items_per_page());

// Format dates
$formattedDate = format_date($user->created_at);

// Check upload constraints
if ($file->getSize() > max_upload_size() * 1024) {
    // File too large
}

if (!is_allowed_file($file->getClientOriginalName())) {
    // File type not allowed
}
```

#### In Blade Views
```blade
<!-- Display formatted dates -->
<p>Created: @formatDate($model->created_at)</p>
<p>Time: @formatTime($model->updated_at)</p>
<p>Full: @formatDatetime($model->created_at)</p>

<!-- Access settings -->
<p>Currency: @currency</p>
<p>App: @setting('app_name', 'SalesPilot')</p>

<!-- Use shared variables -->
<p>{{ $systemCurrency }}</p>
<p>{{ $systemTimezone }}</p>
```

#### Helper Functions Reference
```php
// Currency
default_currency()          // Get default currency code
currency_symbol()           // Get currency symbol
currency_code()             // Get legacy currency code

// Date & Time
default_timezone()          // Get system timezone
system_date_format()        // Get date format
system_time_format()        // Get time format
system_datetime_format()    // Get combined datetime format
format_date($date)          // Format date
format_time($time)          // Format time
format_datetime($datetime)  // Format datetime

// Pagination & Limits
items_per_page()            // Get items per page
session_timeout()           // Get session timeout (minutes)
max_upload_size()           // Get max upload size (KB)
max_upload_size_mb()        // Get max upload size (MB)

// File Validation
allowed_file_types()        // Get array of allowed extensions
is_allowed_file($filename)  // Check if file type is allowed

// General Settings
setting($key, $default)     // Get any setting by key
update_setting($key, $val)  // Update a setting
app_name()                  // Get application name
```

## View Variables (Available in All Views)

These variables are automatically shared with all Blade templates:

```php
$systemCurrency      // Default currency (e.g., 'NGN')
$systemTimezone      // System timezone (e.g., 'Africa/Lagos')
$systemDateFormat    // Date format (e.g., 'Y-m-d')
$systemTimeFormat    // Time format (e.g., 'H:i:s')
$appName             // Application name
$primaryColor        // Primary brand color
$secondaryColor      // Secondary brand color
```

## Blade Directives

Custom Blade directives for easy access to settings:

```blade
@setting('key', 'default')     // Get any setting
@formatDate($date)             // Format date
@formatTime($time)             // Format time
@formatDatetime($datetime)     // Format datetime
@currency                      // Display currency code
```

## Middleware Application

The `ApplySystemPreferences` middleware is automatically applied to all web requests and:
- Sets the timezone for each request
- Applies upload size limits
- Ensures consistency across the application

## Cache Management

Settings are cached for performance. The cache is automatically cleared when:
- Settings are updated via the System Preferences page
- The `AppSetting::set()` method is called
- `php artisan config:clear` is run

To manually clear all settings cache:
```bash
php artisan cache:clear
```

## Best Practices

### For Developers

1. **Always use helper functions** instead of hardcoding values:
   ```php
   // ❌ Bad
   $users->paginate(10);
   
   // ✅ Good
   $users->paginate(items_per_page());
   ```

2. **Use format functions for dates**:
   ```php
   // ❌ Bad
   $date->format('Y-m-d');
   
   // ✅ Good
   format_date($date);
   ```

3. **Validate file uploads using system settings**:
   ```php
   // ❌ Bad
   if ($file->getSize() > 2048000) { }
   
   // ✅ Good
   if ($file->getSize() > max_upload_size() * 1024) { }
   ```

4. **Access settings via helpers, not directly from database**:
   ```php
   // ❌ Bad
   $currency = AppSetting::where('key', 'default_currency')->first()->value;
   
   // ✅ Good
   $currency = default_currency();
   ```

### For Superadmins

1. **Test changes in development** before applying to production
2. **Document setting changes** for your team
3. **Consider timezone impact** on scheduled tasks and reports
4. **Adjust items per page** based on server performance
5. **Set upload limits** based on server capacity and storage

## Troubleshooting

### Settings not reflecting
1. Clear application cache: `php artisan cache:clear`
2. Clear config cache: `php artisan config:clear`
3. Clear view cache: `php artisan view:clear`
4. Hard refresh browser: Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

### Database errors
1. Run migrations: `php artisan migrate`
2. Check `app_settings` table exists
3. Verify setting keys match exactly

### Performance issues
- Settings are cached for 1 hour by default
- Database queries are minimized through caching
- Consider adjusting cache duration in `AppSetting::get()` method

## Migration & Setup

The system preferences are automatically seeded during migration:
```bash
php artisan migrate
```

This creates the `app_settings` table and populates it with default values.

## File Locations

- **Controller**: `app/Http/Controllers/Superadmin/SystemPreferencesController.php`
- **View**: `resources/views/superadmin/settings/system_preferences.blade.php`
- **Middleware**: `app/Http/Middleware/ApplySystemPreferences.php`
- **Helpers**: `app/Helpers/SettingsHelper.php`
- **Service Provider**: `app/Providers/AppServiceProvider.php`
- **Model**: `app/Models/AppSetting.php`
- **Migration**: `database/migrations/2026_05_20_000000_add_system_preferences_settings.php`

## Support

For issues or questions about system preferences, contact your development team or refer to this documentation.
