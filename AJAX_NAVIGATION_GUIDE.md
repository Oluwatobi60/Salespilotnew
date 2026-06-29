# AJAX Navigation System Documentation

## Overview
The AJAX Navigation system allows sidebar links to load content without refreshing the entire page. This provides a faster, more responsive user experience.

## Files Involved

1. **`public/manager_asset/js/ajax-navigation.js`** - Main AJAX handler script
2. **`resources/views/manager/layouts/layout.blade.php`** - Updated layout with AJAX script included

## Features

✅ **Page Content Loading** - Loads page content via AJAX
✅ **Loading Indicator** - Shows spinner while content is loading
✅ **Smooth Transitions** - Fade effect when replacing content
✅ **Browser History** - Back/Forward buttons work correctly
✅ **Page Title Update** - Browser title updates with each page load
✅ **Active Link Styling** - Current page link is highlighted
✅ **Event Dispatching** - Custom events for further scripting

## How It Works

1. User clicks a navigation link
2. JavaScript intercepts the click
3. AJAX request fetches page content
4. Content is displayed in the main panel
5. Browser history is updated
6. Page title is updated

## Exclude Links from AJAX Loading

To make a link load the full page instead of via AJAX, add the `data-ajax-disabled` attribute:

```html
<a class="nav-link" href="{{ route('page') }}" data-ajax-disabled>
  <span class="menu-title">Full Page Load</span>
</a>
```

## Automatic AJAX Handling

These links **automatically** use regular page loads (not AJAX):
- Links with `data-bs-toggle="collapse"` (collapsible menus)
- Links with `data-bs-toggle="dropdown"` (dropdowns)
- Links with `href="#"` (hash links)
- Form submissions

## Custom Events

After AJAX content is loaded, you can listen for these events:

### ajaxContentLoaded
Fired after content is loaded and DOM is updated:
```javascript
document.addEventListener('ajaxContentLoaded', function(e) {
  console.log('New content loaded from:', e.detail.url);
  // Re-initialize any plugins if needed
});
```

### ajaxContentInitialized
Fired when new content is ready for plugin initialization:
```javascript
document.addEventListener('ajaxContentInitialized', function() {
  // Re-initialize DataTables, charts, or other libraries
});
```

## Handling Forms and Modals

If your page-specific scripts need to work with AJAX-loaded content:

1. Use event delegation with `document.addEventListener()`
2. Or listen for `ajaxContentInitialized` and re-initialize scripts
3. Example:
```javascript
document.addEventListener('ajaxContentInitialized', function() {
  // Re-initialize any Datatable or charts
  initializeDataTable();
});
```

## Browser Compatibility

- Chrome/Chromium: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Edge: ✅ Full support
- IE 11: ❌ Not supported

## Performance Benefits

- **Eliminates page refresh overhead** - Only content area is updated
- **Faster navigation** - Reduces bandwidth usage
- **Smoother UX** - Fade transitions instead of flashing
- **Instant back/forward** - Browser history works seamlessly

## Disabling AJAX Navigation

To completely disable AJAX navigation, comment out or remove the script line:
```html
<!-- <script src="{{ asset('manager_asset/js/ajax-navigation.js') }}"></script> -->
```

## Troubleshooting

### Content not loading
- Check browser console for errors (F12)
- Ensure the target URL returns valid HTML
- Verify `.content-wrapper` element exists in response

### Page title not updating
- Ensure the response HTML has a `<title>` tag
- Check browser console for parsing errors

### Event listeners not working
- Re-initialize your scripts in `ajaxContentInitialized` event listener
- Use event delegation for dynamically added elements

### Links not working
- Check if link has `data-ajax-disabled` attribute
- Verify link is not a hash link (`href="#"`)
- Check console for JavaScript errors

## Performance Tips

1. **Optimize server responses** - Remove unnecessary HTML from responses
2. **Cache static assets** - Let browser cache CSS/JS files
3. **Use lazy loading** - Load images/content only when needed
4. **Minimize CSS/JS** - Reduce payload size

## Future Enhancements

Potential improvements:
- [ ] Add request caching/memoization
- [ ] Support for form submissions via AJAX
- [ ] Advanced error handling with retry logic
- [ ] Analytics tracking for page views
- [ ] Keyboard navigation support
- [ ] Page state preservation (scroll position, form data)
