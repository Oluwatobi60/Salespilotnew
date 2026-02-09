# Supplier Panel Component

## Overview
Reusable supplier creation panel component for item forms (standard and variant items).

## Files Created
1. **Blade Component**: `resources/views/components/supplier-panel.blade.php` (56 lines)
2. **JavaScript Module**: `public/manager_asset/js/components/supplier-panel.js` (336 lines)

## Features
- ✅ Side panel overlay with slide-in animation
- ✅ Form validation (required name & email)
- ✅ AJAX submission to `/manager/supplier/create`
- ✅ Automatic dropdown update after successful creation
- ✅ Select2 integration
- ✅ SweetAlert2 notifications
- ✅ Error handling (validation & general errors)
- ✅ Auto-initialization via jQuery ready
- ✅ Manual initialization support
- ✅ Escape key to close
- ✅ Click overlay to close

## Usage

### 1. Include Component in Blade File
```blade
<!-- Add before @endsection -->
<x-supplier-panel />

<!-- Include scripts -->
<script src="{{ asset('manager_asset/js/components/supplier-panel.js') }}"></script>
```

### 2. Add Dropdown Option
```blade
<select id="supplier" name="supplier_id">
    <option value="">Select Supplier</option>
    <!-- ...existing options... -->
    <option value="add_new_supplier">
        <i class="mdi mdi-plus"></i> + Add New Supplier
    </option>
</select>
```

### 3. Initialize Component (Optional)
```javascript
// Auto-initializes if panel exists, but you can also manually initialize:
if (typeof SupplierPanel !== 'undefined') {
  SupplierPanel.init('supplier'); // Pass the select dropdown ID
}
```

## API

### SupplierPanel.init(supplierSelectId)
Initializes the supplier panel component.

**Parameters:**
- `supplierSelectId` (string, default: 'supplier'): ID of the supplier select dropdown

**Returns:** `boolean` - `true` if initialization successful, `false` otherwise

**Example:**
```javascript
SupplierPanel.init('supplier');
```

### SupplierPanel.open()
Opens the supplier panel.

**Example:**
```javascript
SupplierPanel.open();
```

### SupplierPanel.close()
Closes the supplier panel and resets the form.

**Example:**
```javascript
SupplierPanel.close();
```

## Form Fields
- **Supplier Name** (required): 1-255 characters
- **Email** (required): Valid email format
- **Contact Person** (optional)
- **Phone Number** (optional)
- **Address** (optional)

## Backend Endpoint
**POST** `/manager/supplier/create`

**Request Body:**
```json
{
  "name": "ABC Suppliers Inc.",
  "email": "contact@abcsuppliers.com",
  "contact_person": "John Doe",
  "phone": "+1234567890",
  "address": "123 Main St, City, Country"
}
```

**Success Response:**
```json
{
  "supplier": {
    "id": 123,
    "name": "ABC Suppliers Inc.",
    "email": "contact@abcsuppliers.com"
  }
}
```

**Error Response:**
```json
{
  "message": "Validation failed",
  "errors": {
    "name": ["The supplier name is required."],
    "email": ["The email format is invalid."]
  }
}
```

## Integration

### Standard Items
**File:** `add_item_standard.blade.php`
- Replaced 54 lines of HTML with `<x-supplier-panel />`
- Added script include for `supplier-panel.js`

**File:** `add_item_standard.js`
- Removed ~220 lines of supplier code
- Added initialization: `SupplierPanel.init('supplier')`

### Variant Items
**File:** `add_item_variant.blade.php`
- Replaced 54 lines of HTML with `<x-supplier-panel />`
- Added script include for `supplier-panel.js`

**File:** `add_item_variant.js`
- Removed ~186 lines of supplier code
- Added retry-based initialization (same pattern as CategoryPanel)

## Code Reduction
- **HTML**: ~108 lines eliminated (54 lines × 2 files)
- **JavaScript**: ~406 lines eliminated (220 + 186 lines)
- **Total**: ~514 lines of duplicate code removed
- **Component**: 392 lines (56 HTML + 336 JS) - reusable across unlimited pages

## Customization

### Change Supplier Select ID
If your dropdown has a different ID:
```javascript
SupplierPanel.init('my_custom_supplier_select');
```

### Custom Validation
Edit `supplier-panel.js` → `handleSubmit()` method to add custom validation rules.

### Custom Success Handler
Edit `supplier-panel.js` → `handleSuccess()` method to customize behavior after supplier creation.

## Dependencies
- **jQuery**: For auto-initialization
- **Select2**: For dropdown enhancement (optional, auto-detected)
- **SweetAlert2**: For notifications (optional, falls back to `alert()`)
- **Bootstrap 5**: For styling and form classes
- **Laravel CSRF**: `@csrf` token required

## Browser Support
- Modern browsers (ES6+ required)
- IIFE pattern used for immediate initialization
- Fetch API for AJAX requests

## Troubleshooting

### Panel doesn't open
Check console for:
```javascript
SupplierPanel - Panel element not found!
SupplierPanel - Supplier select element not found!
```

Solution: Ensure both `<x-supplier-panel />` is included and select has correct ID.

### Auto-initialization not working
Supplier panel auto-initializes when:
1. jQuery is loaded
2. `$(document).ready()` fires
3. Element `#addSupplierPanel` exists

For manual control, call `SupplierPanel.init('supplier')` in your page script.

### AJAX errors
Check:
1. CSRF token is present (`@csrf` in form)
2. Endpoint `/manager/supplier/create` exists
3. User is authenticated
4. Server returns proper JSON response

## Future Enhancements
- [ ] Support for multiple supplier selects on same page
- [ ] Configurable endpoint URL
- [ ] Custom field mapping
- [ ] Internationalization (i18n)
- [ ] Unit tests
- [ ] TypeScript definitions

## Related Components
- **Category Panel**: `category-panel.blade.php` / `category-panel.js`

## Version
**1.0.0** - Initial release (February 2026)

## License
Part of SalesPilot Laravel application.
