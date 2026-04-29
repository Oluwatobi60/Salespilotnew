# Feature Hierarchy & Control Guide

## Overview
Features are now organized by **user hierarchy** to match your actual business structure:
- **Business Creator** (Owner) → **Manager** (Added by owner) → **Staff** (Added by manager/owner)

## User Hierarchy

### Business Creator (Owner)
- **Identified by**: `addby = null` or `addby = own email`
- **Role**: Complete business control
- **Feature Prefix**: None (e.g., `pos_system`, `advanced_inventory`)
- **Features** (15 total):
  1. `owner_dashboard` - Complete business overview
  2. `manage_managers` - Add/edit/remove managers
  3. `manage_staff` - Add/edit/remove staff
  4. `multi_branch` - Create and manage branches
  5. `stock_transfer` - Transfer stock between branches
  6. `advanced_inventory` - Full inventory control
  7. `supplier_management` - Manage suppliers
  8. `customer_management` - Manage customers
  9. `pos_system` - POS system access
  10. `advanced_reports` - All reports and analytics
  11. `activity_logs` - View all activity logs
  12. `discounts_promotions` - Create/manage discounts
  13. `profit_loss_reports` - Financial reports
  14. `export_data` - Export data to CSV/Excel
  15. `system_preferences` - Configure business settings

### Manager (Added by Owner)
- **Identified by**: `addby = business_creator_email`
- **Role**: Operational management
- **Feature Prefix**: `manager_*`
- **Features** (10 total):
  1. `manager_dashboard` - Manager-level dashboard
  2. `manager_manage_staff` - Add and manage staff
  3. `manager_view_branches` - View assigned branches
  4. `manager_inventory` - Manage branch inventory
  5. `manager_suppliers` - View/manage suppliers
  6. `manager_customers` - View/manage customers
  7. `manager_pos` - Process sales
  8. `manager_reports` - Branch reports
  9. `manager_activity_logs` - Branch activity logs
  10. `manager_discounts` - Apply approved discounts

### Staff (Added by Manager/Owner)
- **Identified by**: `addby = manager_email` or `addby = owner_email`
- **Role**: Daily operations
- **Feature Prefix**: `staff_*`
- **Features** (6 total):
  1. `staff_dashboard` - Basic task dashboard
  2. `staff_pos` - Process customer sales
  3. `staff_view_inventory` - View stock levels
  4. `staff_reports` - Own sales reports
  5. `staff_discounts` - Apply approved discounts
  6. `staff_customers` - View customer info during sales

## How Features are Checked in Navigation

The navigation automatically detects the user's role and checks the appropriate features:

```php
@php 
  $manager = Auth::user(); 
  $isBusinessCreator = empty($manager->addby); // Owner has no addby
  
  // Feature slug mapping based on role
  $posFeature = $isBusinessCreator ? 'pos_system' : 'manager_pos';
  $inventoryFeature = $isBusinessCreator ? 'advanced_inventory' : 'manager_inventory';
  // ... etc
@endphp

@if(user_has_feature($posFeature, $manager))
  <!-- Show POS menu -->
@endif
```

## Enabling Features from Superadmin

1. Login to superadmin panel
2. Navigate to **Settings** → **Plan Features**
3. Select a subscription plan (Free, Basic, Standard, Premium)
4. You'll see 3 role sections:
   - **Business Creator** (red badge) - 15 features
   - **Manager** (blue badge) - 10 features
   - **Staff** (green badge) - 6 features
5. **Check the boxes** to enable features for that plan
6. Changes take effect immediately (no cache clear needed)

## Navigation Menu Control

### Business Creator Sees:
- **Sell** (if `pos_system` enabled)
- **Sales** (if `advanced_reports` enabled)
- **Reports** (if `advanced_reports` enabled)
- **CRM** (if `customer_management` or `discounts_promotions` enabled)
- **Add Staff** → Staffs (if `manage_staff` enabled)
- **Add Staff** → Managers (if `manage_managers` enabled)
- **Add Branches** (if `multi_branch` enabled)
- **Activity Logs** (if `activity_logs` enabled)
- **Inventory** (if `advanced_inventory` enabled)
- **Suppliers** (if `supplier_management` enabled)

### Manager Sees:
- **Sell** (if `manager_pos` enabled)
- **Sales** (if `manager_reports` enabled)
- **Reports** (if `manager_reports` enabled)
- **CRM** (if `manager_customers` or `manager_discounts` enabled)
- **Manage Staff** (if `manager_manage_staff` enabled)
- **View Branches** (if `manager_view_branches` enabled)
- **Activity Logs** (if `manager_activity_logs` enabled)
- **Inventory** (if `manager_inventory` enabled)
- **Suppliers** (if `manager_suppliers` enabled)

### Staff Sees:
- **Sell** (if `staff_pos` enabled)
- **Sales** (if `staff_reports` enabled)
- **Reports** (if `staff_reports` enabled)
- **CRM** (if `staff_customers` or `staff_discounts` enabled)
- **Inventory** (view only, if `staff_view_inventory` enabled)

## Testing the Feature System

### Step 1: Disable All Features
1. Login as superadmin
2. Go to **Plan Features**
3. Select your test plan (e.g., "Standard")
4. **Uncheck all boxes** in all 3 roles
5. Save/refresh

### Step 2: Test as Business Creator
1. Login with a business creator account (addby = null)
2. You should see **NO menus** except Home
3. Go back to superadmin and enable `pos_system`
4. Refresh as business creator → **Sell menu appears**
5. Enable more features one by one to test

### Step 3: Test as Manager
1. Create a manager account (addby = business creator email)
2. Enable `manager_pos` in superadmin
3. Login as manager → **Sell menu appears**
4. Test other manager features

### Step 4: Test as Staff
1. Create a staff account (addby = manager email)
2. Enable `staff_pos` in superadmin
3. Login as staff → **Sell menu appears**
4. Test other staff features

## Helper Functions

Use these functions anywhere in your code:

```php
// Check if user has a specific feature
user_has_feature('pos_system', $user); // Returns true/false

// Check if a plan has a feature
plan_has_feature($plan, 'advanced_inventory'); // Returns true/false

// Get all features for a user's subscription
$features = user_subscription_features($user); // Returns array of slugs
```

## Route Protection (Future Implementation)

Currently, only **navigation menus** are controlled by features. For complete security, you should also add:

### Middleware Protection
```php
Route::middleware(['auth', 'check.feature:pos_system'])->group(function () {
    Route::get('/sell', [SellController::class, 'index'])->name('manager.sell_product');
});
```

### Controller Protection
```php
public function index()
{
    if (!user_has_feature('pos_system')) {
        abort(403, 'You do not have access to this feature.');
    }
    
    // ... rest of your code
}
```

## Important Notes

1. **Feature prefixes are critical**: Manager features MUST have `manager_` prefix, staff features MUST have `staff_` prefix
2. **Business creator features have NO prefix**: They use clean names like `pos_system`, `advanced_inventory`
3. **Changes are immediate**: No cache clear needed when toggling features in superadmin
4. **Navigation auto-detects role**: Based on `addby` column value
5. **All plans start with 0 features**: Superadmin must manually enable features for each plan

## Troubleshooting

### "I enabled a feature but the menu doesn't show"
- Clear browser cache/cookies
- Run `php artisan optimize:clear`
- Check if user's subscription is active
- Verify the feature slug matches exactly (case-sensitive)

### "Wrong features showing for my role"
- Check the `addby` column in your user record
- Business Creator: `addby = null`
- Manager: `addby = owner_email`
- Staff: `addby = manager_email or owner_email`

### "All menus still showing when features disabled"
- Ensure you unchecked the boxes in **all 3 roles** (Business Creator, Manager, Staff)
- Clear Laravel cache: `php artisan optimize:clear`
- Check that the plan is associated with the user's subscription

## Quick Reference: Feature → Menu Mapping

| Feature | Shows Menu | Roles |
|---------|-----------|-------|
| `pos_system` | Sell | Business Creator |
| `manager_pos` | Sell | Manager |
| `staff_pos` | Sell | Staff |
| `advanced_inventory` | Inventory | Business Creator |
| `manager_inventory` | Inventory | Manager |
| `staff_view_inventory` | Inventory (view) | Staff |
| `advanced_reports` | Sales, Reports | Business Creator |
| `manager_reports` | Sales, Reports | Manager |
| `staff_reports` | Sales, Reports | Staff |
| `customer_management` | CRM → Customers | Business Creator |
| `manager_customers` | CRM → Customers | Manager |
| `staff_customers` | CRM → Customers | Staff |
| `discounts_promotions` | CRM → Discount | Business Creator |
| `manager_discounts` | CRM → Discount | Manager |
| `staff_discounts` | Apply discounts | Staff |
| `manage_managers` | Add Staff → Managers | Business Creator |
| `manage_staff` | Add Staff → Staffs | Business Creator |
| `manager_manage_staff` | Manage Staff | Manager |
| `multi_branch` | Add Branches | Business Creator |
| `manager_view_branches` | View Branches | Manager |
| `activity_logs` | Activity Logs | Business Creator |
| `manager_activity_logs` | Activity Logs | Manager |
| `supplier_management` | Suppliers | Business Creator |
| `manager_suppliers` | Suppliers | Manager |

---

**Last Updated**: April 29, 2026  
**Total Features**: 31 (15 Business Creator + 10 Manager + 6 Staff)  
**Database Table**: `subscription_features`  
**Helper File**: `app/Helpers/SettingsHelper.php`
