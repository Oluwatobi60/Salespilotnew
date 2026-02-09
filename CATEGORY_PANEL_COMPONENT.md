# Category Panel Component

A reusable component for adding new categories on-the-fly in Laravel Blade templates.

## Files Created

1. **Blade Component**: `resources/views/components/category-panel.blade.php`
2. **JavaScript Module**: `public/manager_asset/js/components/category-panel.js`

## Installation

### Step 1: Include the Blade Component

Add the component to your Blade template (before the closing body tag or in your content section):

```blade
<!-- Include the category panel component -->
<x-category-panel />
```

### Step 2: Include the JavaScript File

Add the JavaScript file to your template:

```blade
<script src="{{ asset('manager_asset/js/components/category-panel.js') }}"></script>
```

### Step 3: Add the Option to Your Select Dropdown

In your category select dropdown, add the "+ Add New Category" option:

```blade
<select class="form-select" id="category" name="category" required>
    <option value="">Select Category</option>
    @foreach($categories as $category)
        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
    @endforeach
    <option value="add_new_category" style="color: #007bff; font-weight: 600;">
        <i class="mdi mdi-plus"></i> + Add New Category
    </option>
</select>
```

## Usage Examples

### Basic Usage (Auto-initialization)

If you include the component and script, it will automatically initialize when the DOM is ready:

```blade
<!-- In your blade template -->
@extends('manager.layouts.layout')

@section('content')
    <!-- Your form with category select -->
    <select class="form-select" id="category" name="category" required>
        <!-- options here -->
        <option value="add_new_category">+ Add New Category</option>
    </select>

    <!-- Include component at the bottom -->
    <x-category-panel />
@endsection

@section('scripts')
    <script src="{{ asset('manager_asset/js/components/category-panel.js') }}"></script>
@endsection
```

### Manual Initialization

If you want to initialize manually or use a custom select ID:

```javascript
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize with default ID 'category'
    CategoryPanel.init();
    
    // OR initialize with custom select ID
    CategoryPanel.init('myCustomCategorySelect');
});
```

### Programmatically Open/Close

You can control the panel programmatically:

```javascript
// Open the panel
CategoryPanel.open();

// Close the panel
CategoryPanel.close();
```

## API Reference

### Methods

#### `CategoryPanel.init(categorySelectId)`
Initialize the category panel component.

**Parameters:**
- `categorySelectId` (string, optional): ID of the category select element. Default: `'category'`

**Example:**
```javascript
CategoryPanel.init('productCategory');
```

#### `CategoryPanel.open()`
Open the category panel.

**Example:**
```javascript
CategoryPanel.open();
```

#### `CategoryPanel.close()`
Close the category panel.

**Example:**
```javascript
CategoryPanel.close();
```

## Features

- ✅ **Slide-in side panel** for adding categories
- ✅ **Form validation** (5-100 characters)
- ✅ **AJAX submission** without page reload
- ✅ **Automatic dropdown update** after creation
- ✅ **Select2 integration** (if available)
- ✅ **SweetAlert2 notifications** (if available)
- ✅ **Escape key to close**
- ✅ **Click overlay to close**
- ✅ **Responsive design**
- ✅ **Loading states**
- ✅ **Error handling**

## Backend Requirements

The component expects a POST endpoint at `/manager/category/create` that:

1. Accepts JSON payload: `{ "category_name": "string" }`
2. Returns JSON response on success:
```json
{
    "category": {
        "id": 1,
        "category_name": "New Category"
    }
}
```
3. Returns validation errors in standard Laravel format

## CSS Requirements

The component uses these CSS classes (should be defined in your stylesheet):

- `.category-panel-overlay` - Semi-transparent overlay
- `.category-side-panel` - Side panel container
- `.category-side-panel.active` - Active state
- `.category-panel-header` - Panel header
- `.category-panel-body` - Panel body
- `.category-panel-footer` - Panel footer
- `.category-close-btn` - Close button

Example CSS is in `public/manager_asset/css/add_item_standard.css`

## Example Implementation

### Variant Items Page

```blade
@extends('manager.layouts.layout')

@section('manager_layout_content')
    <form id="addVariantForm">
        <select class="form-select" id="category" name="category" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
            <option value="add_new_category" style="color: #007bff; font-weight: 600;">
                + Add New Category
            </option>
        </select>
    </form>

    <!-- Include component -->
    <x-category-panel />
@endsection

@push('scripts')
    <script src="{{ asset('manager_asset/js/components/category-panel.js') }}"></script>
@endpush
```

## Customization

### Using Different Select IDs

```javascript
// For multiple category selects on the same page
CategoryPanel.init('mainCategory');  // Primary category
CategoryPanel.init('subCategory');    // Sub category
```

### Custom Success Handler

Modify the `handleSuccess` method in the component file to customize behavior after category creation.

## Troubleshooting

### Panel doesn't open
- Check if the component is included: `<x-category-panel />`
- Verify JavaScript is loaded
- Check browser console for errors
- Ensure select has option with value="add_new_category"

### Form doesn't submit
- Check CSRF token is present
- Verify `/manager/category/create` route exists
- Check network tab for request/response

### Select2 doesn't update
- Ensure Select2 is initialized before CategoryPanel
- Use `$('#category').select2()` before `CategoryPanel.init()`

## Migration Guide

### From Inline Code to Component

**Before:**
```blade
<!-- Inline HTML in your template -->
<div class="category-panel-overlay" id="categoryPanelOverlay"></div>
<div class="category-side-panel" id="addCategoryPanel">
    <!-- ... -->
</div>

<script>
    // Inline JavaScript
    function openCategoryPanel() { ... }
</script>
```

**After:**
```blade
<!-- Clean component inclusion -->
<x-category-panel />

<script src="{{ asset('manager_asset/js/components/category-panel.js') }}"></script>
```

## License

Part of SalesPilot Laravel Application
