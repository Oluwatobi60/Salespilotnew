# Superadmin Settings - Quick Reference

## ✅ What Was Created

### 1. Database & Models
- ✅ **Migration**: `2026_04_29_100000_create_app_settings_table.php`
  - Creates `app_settings` table
  - Populates 41 default settings across 6 categories
- ✅ **Model**: `app/Models/AppSetting.php`
  - Cached settings retrieval
  - Get/Set methods
  - Auto-cache clearing

### 2. Controller & Routes
- ✅ **Controller**: `app/Http/Controllers/Superadmin/SettingsController.php`
  - Settings dashboard
  - Update settings
  - Test email
  - Clear cache
  - Run backup
  - Toggle maintenance mode
- ✅ **Routes**: 6 new routes under `/superadmin/settings`

### 3. Views & UI
- ✅ **View**: `resources/views/superadmin/settings/index.blade.php`
  - Widget-based dashboard
  - 6 category widgets
  - Quick action buttons
  - Test email modal
- ✅ **Styles**: Enhanced `public/superadmin_asset/css/superadmin_layout.css`
  - Widget styling
  - Button hover effects
  - Form enhancements
- ✅ **Sidebar**: Updated with "Settings" link under "System" section

### 4. Helper Functions
- ✅ **Helper File**: `app/Helpers/SettingsHelper.php`
  - 15 helper functions
  - Auto-loaded via Composer
  - Easy settings access

### 5. Documentation
- ✅ **Complete Guide**: `SUPERADMIN_SETTINGS_GUIDE.md`
  - 500+ lines of documentation
  - Usage examples
  - Troubleshooting
  - Best practices

---

## 🚀 Quick Start

### Access Settings
1. Login as superadmin
2. Click **System → Settings** in sidebar
3. Configure your settings
4. Click **Save All Settings**

### URL
```
https://your-domain.com/superadmin/settings
```

---

## 📦 Settings Categories

| Widget | Icon | Settings Count | Examples |
|--------|------|----------------|----------|
| **General** | Purple Gear | 7 | App name, timezone, maintenance |
| **Email** | Blue Envelope | 8 | SMTP host, port, credentials |
| **Payment** | Green Card | 9 | Paystack keys, bank details |
| **System** | Orange CPU | 7 | Registration, backups, session |
| **Appearance** | Pink Palette | 5 | Logo, colors, footer |
| **Security** | Red Shield | 5 | Passwords, 2FA, HTTPS |

**Total**: 41 configurable settings

---

## 💡 Usage Examples

### In PHP/Controllers
```php
// Get any setting
$appName = setting('app_name', 'SalesPilot');

// Specialized helpers
$logo = app_logo();
$email = support_email();
$color = primary_color();

// Check booleans
if (is_maintenance_mode()) { }
if (is_registration_enabled()) { }
```

### In Blade Views
```blade
<h1>{{ app_name() }}</h1>
<img src="{{ app_logo() }}" alt="Logo">
<a href="mailto:{{ support_email() }}">Support</a>
<div style="color: {{ primary_color() }}">...</div>
```

### Update Setting
```php
use App\Models\AppSetting;

AppSetting::set('app_name', 'New Name');
// OR
update_setting('app_name', 'New Name');
```

---

## ⚡ Quick Actions

| Button | Action | When to Use |
|--------|--------|-------------|
| 🔴 **Maintenance** | Enable/disable | Updates, maintenance |
| 🔵 **Clear Cache** | Clear all caches | After settings changes |
| 🟢 **Backup** | Database backup | Before major changes |
| 🔵 **Test Email** | Send test email | Verify SMTP config |

---

## 🔧 Maintenance Mode

### Enable
Click **Enable Maintenance** button

### Access During Maintenance
```
https://your-domain.com/superadmin-bypass
```

### Disable
Click **Disable Maintenance** button or:
```bash
php artisan up
```

---

## 🎨 Customization Examples

### Change Branding
1. Go to **Appearance Settings**
2. Upload logo/favicon
3. Set primary color: `#667eea`
4. Set secondary color: `#764ba2`
5. Update footer text
6. Save

### Configure Email
1. Go to **Email Settings**
2. Set SMTP host: `smtp.gmail.com`
3. Set port: `587`
4. Enter username/password
5. Set encryption: `tls`
6. Click **Test Email** to verify
7. Save

### Setup Paystack
1. Go to **Payment Settings**
2. Enter public key: `pk_test_...`
3. Enter secret key: `sk_test_...`
4. Enable Paystack toggle
5. Save

---

## 📊 Helper Functions Reference

| Function | Returns | Example |
|----------|---------|---------|
| `setting($key, $default)` | mixed | `setting('app_name')` |
| `settings($group)` | Collection | `settings('email')` |
| `update_setting($key, $value)` | bool | `update_setting('key', 'val')` |
| `app_name()` | string | `SalesPilot` |
| `app_logo()` | string\|null | `/storage/logos/logo.png` |
| `support_email()` | string | `support@salespilot.com` |
| `support_phone()` | string | `+234 800 000 0000` |
| `primary_color()` | string | `#667eea` |
| `secondary_color()` | string | `#764ba2` |
| `is_maintenance_mode()` | bool | `true`/`false` |
| `is_registration_enabled()` | bool | `true`/`false` |
| `currency_symbol()` | string | `₦` |
| `currency_code()` | string | `NGN` |

---

## 🐛 Troubleshooting

### Settings Not Updating
```bash
php artisan config:clear
php artisan cache:clear
```
Or use **Clear Cache** button

### Email Not Sending
1. Click **Test Email** button
2. Check SMTP credentials
3. Verify port (587 for TLS)
4. Check logs: `storage/logs/laravel.log`

### File Upload Issues
```bash
chmod -R 775 storage
php artisan storage:link
```

---

## 🔐 Security Notes

- ✅ Only superadmins can access settings
- ✅ Passwords are masked in UI
- ✅ API keys are hidden
- ✅ Settings are cached for performance
- ✅ All changes are logged

---

## 📝 Files Created/Modified

### New Files (8)
1. `database/migrations/2026_04_29_100000_create_app_settings_table.php`
2. `app/Models/AppSetting.php`
3. `app/Http/Controllers/Superadmin/SettingsController.php`
4. `app/Helpers/SettingsHelper.php`
5. `resources/views/superadmin/settings/index.blade.php`
6. `SUPERADMIN_SETTINGS_GUIDE.md`
7. `SUPERADMIN_SETTINGS_QUICK_REFERENCE.md` (this file)

### Modified Files (4)
1. `routes/web.php` - Added settings routes
2. `composer.json` - Added helper autoload
3. `resources/views/superadmin/layouts/layout.blade.php` - Added Settings link
4. `public/superadmin_asset/css/superadmin_layout.css` - Added widget styles

---

## ✨ Features

- 🎯 **Widget-Based UI** - Organized, beautiful interface
- ⚡ **Quick Actions** - One-click maintenance, cache, backup
- 🔄 **Auto-Caching** - Fast performance
- 📧 **Test Email** - Verify SMTP configuration
- 🎨 **File Uploads** - Logo and favicon support
- 🌈 **Color Picker** - Brand color customization
- 📱 **Responsive** - Works on all devices
- 🔒 **Secure** - Superadmin-only access
- 📖 **Well Documented** - Complete guide included

---

## 🎯 Benefits

✅ **No More Hardcoding** - Everything configurable from UI
✅ **Zero Downtime** - Update settings without redeploying
✅ **Centralized Control** - All settings in one place
✅ **Easy Maintenance** - Built-in maintenance mode
✅ **Developer Friendly** - Simple helper functions
✅ **Future Proof** - Easy to add new settings

---

## 📚 Documentation

- **Full Guide**: `SUPERADMIN_SETTINGS_GUIDE.md` (500+ lines)
- **Quick Reference**: This file
- **Code Comments**: Inline documentation
- **Helper Functions**: Well documented

---

## ✅ Setup Checklist

- [x] Migration created and run
- [x] 41 default settings loaded
- [x] Model with caching created
- [x] Controller with all actions
- [x] 6 routes registered
- [x] Settings view with widgets
- [x] Helper functions auto-loaded
- [x] Sidebar link added
- [x] Styles enhanced
- [x] Documentation complete

**Status**: ✅ **READY TO USE**

---

## 🚀 Next Steps

1. **Access Settings**: `/superadmin/settings`
2. **Configure App Name**: Update from "SalesPilot"
3. **Setup Email**: Configure SMTP settings
4. **Add Logo**: Upload your logo
5. **Set Colors**: Choose brand colors
6. **Test Email**: Verify email configuration
7. **Configure Payments**: Add Paystack keys

---

**System Status**: ✅ Fully Operational  
**Settings Count**: 41  
**Categories**: 6  
**Helper Functions**: 15  
**Documentation**: Complete  

**Built for SalesPilot** 🚀
