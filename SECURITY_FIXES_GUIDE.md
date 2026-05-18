# 🔧 Quick Security Fixes - Implementation Guide

## CRITICAL FIX #1: File Upload Security (15 minutes)

### Files to Update:
1. `app/Http/Controllers/Manager/StandardItemController.php`
2. `app/Http/Controllers/Manager/VariantItemController.php`
3. `app/Http/Controllers/Manager/StaffMainController.php`
4. `app/Http/Controllers/Manager/AddManagerController.php`

### Steps:

#### 1. Update Validation Rules
**Find this pattern in all controllers:**
```php
if ($request->hasFile('item_image')) {
    $image = $request->file('item_image');
    $imageName = time() . '_' . $image->getClientOriginalName();
    $image->move(public_path('uploads/item_images'), $imageName);
    $validatedData['item_image'] = 'uploads/item_images/' . $imageName;
}
```

**Replace with:**
```php
// Add to validation array
'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',

// Replace file handling code
if ($request->hasFile('item_image')) {
    // Store using Laravel's secure method
    $path = $request->file('item_image')->store('item_images', 'public');
    $validatedData['item_image'] = $path;
}
```

#### 2. For Staff Photos
```php
// Validation
'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

// Storage
if ($request->hasFile('passport_photo')) {
    $path = $request->file('passport_photo')->store('staff_photos', 'public');
    $validatedData['passport_photo'] = $path;
}
```

#### 3. For Business Logos
```php
// Validation
'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',

// Storage
if ($request->hasFile('business_logo')) {
    $path = $request->file('business_logo')->store('business_logos', 'public');
    $validatedData['business_logo'] = $path;
}
```

#### 4. Update Views to Use Storage URLs
**In Blade templates, change:**
```blade
<!-- Old -->
<img src="{{ asset($item->item_image) }}" alt="Item">

<!-- New -->
<img src="{{ Storage::url($item->item_image) }}" alt="Item">
```

**Add to top of file:**
```blade
@php
use Illuminate\Support\Facades\Storage;
@endphp
```

---

## CRITICAL FIX #2: Authorization Checks (30 minutes)

### Create Authorization Trait

**Create file:** `app/Traits/AuthorizesBusinessResources.php`

```php
<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesBusinessResources
{
    /**
     * Check if the current user owns this resource
     */
    protected function authorizeResource(Model $resource, string $businessField = 'business_name')
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $userBusinessName = $user->business_name;
        
        // If user was added by another manager, get creator's business
        if ($user->addby) {
            $creator = \App\Models\User::where('email', $user->addby)->first();
            $userBusinessName = $creator ? $creator->business_name : $user->business_name;
        }

        if ($resource->$businessField !== $userBusinessName) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $resource;
    }

    /**
     * Find resource by ID and check ownership
     */
    protected function findAndAuthorize($modelClass, $id, string $businessField = 'business_name')
    {
        $user = Auth::user();
        $userBusinessName = $user->business_name;
        
        if ($user->addby) {
            $creator = \App\Models\User::where('email', $user->addby)->first();
            $userBusinessName = $creator ? $creator->business_name : $user->business_name;
        }

        $resource = $modelClass::where('id', $id)
            ->where($businessField, $userBusinessName)
            ->firstOrFail();

        return $resource;
    }
}
```

### Use the Trait in Controllers

**Example: CategoryController.php**

```php
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\AuthorizesBusinessResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    use AuthorizesBusinessResources;  // ✅ Add this

    public function update_category(Request $request, $id)
    {
        // ✅ SECURE: Check ownership
        $category = $this->findAndAuthorize(Category::class, $id);
        
        $validatedData = $request->validate([
            'category_name' => 'required|max:100|min:5|unique:categories,category_name,' . $category->id,
        ]);

        $category->update($validatedData);
        \App\Helpers\ActivityLogger::log('update_category', 'Updated category: ' . $category->category_name);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        }

        return redirect()->route('all_categories')->with('success', 'Category updated successfully');
    }

    public function delete_category($id)
    {
        // ✅ SECURE: Check ownership
        $category = $this->findAndAuthorize(Category::class, $id);
        
        $categoryName = $category->category_name;
        $category->delete();
        
        \App\Helpers\ActivityLogger::log('delete_category', 'Deleted category: ' . $categoryName);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
```

### Apply to All Controllers

**Controllers that need this:**
- ✅ CategoryController.php
- ✅ SupplierController.php
- ✅ UnitController.php
- ✅ StandardItemController.php
- ✅ VariantItemController.php
- ✅ StaffMainController.php
- ✅ AllItemsController.php

---

## CRITICAL FIX #3: Add Security Headers (5 minutes)

**File:** `bootstrap/app.php`

**Add after line 8:**
```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleManager;
use App\Http\Middleware\CheckSubscriptionStatus;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rolemanager' => RoleManager::class,
            'check.subscription' => CheckSubscriptionStatus::class
        ]);

        // ✅ ADD SECURITY HEADERS
        $middleware->append(function ($request, $next) {
            $response = $next($request);
            
            // Prevent clickjacking
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            
            // Prevent MIME sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            
            // XSS Protection
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // HTTPS Enforcement (only in production)
            if (app()->environment('production')) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }
            
            // Referrer Policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            return $response;
        });

        $middleware->preventRequestsDuringMaintenance([
            'superadmin/*',
            'superadmin-bypass',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## MEDIUM PRIORITY FIX: Enable Session Encryption

**File:** `.env`

```env
# Find and update:
SESSION_ENCRYPT=true
```

---

## MEDIUM PRIORITY FIX: Add Rate Limiting

**File:** `routes/web.php`

**Wrap sensitive routes with throttle:**

```php
// Manager routes - limit to 60 requests per minute
Route::middleware(['auth', 'verified', 'rolemanager:manager', 'check.subscription', 'throttle:60,1'])->group(function () {
    // Existing routes...
});

// Staff routes - limit to 60 requests per minute
Route::middleware(['auth:staff', 'throttle:60,1'])->prefix('staff')->group(function () {
    // Existing routes...
});

// Superadmin routes - limit to 100 requests per minute
Route::middleware(['auth:superadmin', 'throttle:100,1'])->prefix('superadmin')->group(function () {
    // Existing routes...
});
```

---

## TESTING CHECKLIST

After implementing fixes, test:

### File Upload Security
- [ ] Try uploading `.php` file (should be rejected)
- [ ] Try uploading `.exe` file (should be rejected)
- [ ] Try uploading 10MB image (should be rejected)
- [ ] Upload valid JPEG/PNG (should work)
- [ ] Verify file is stored in `storage/app/public/`
- [ ] Verify images display correctly

### Authorization
- [ ] Try accessing another business's category (should get 403)
- [ ] Try editing another business's item (should get 403)
- [ ] Try deleting another business's supplier (should get 403)
- [ ] Verify own resources work normally

### Security Headers
- [ ] Open DevTools → Network → Check response headers
- [ ] Verify `X-Frame-Options: SAMEORIGIN`
- [ ] Verify `X-Content-Type-Options: nosniff`
- [ ] Verify `X-XSS-Protection: 1; mode=block`

### Rate Limiting
- [ ] Make 70+ requests rapidly (should get 429 error)
- [ ] Wait 1 minute (should work again)

---

## DEPLOYMENT CHECKLIST

Before deploying to VPS:

1. **Backup database:**
   ```bash
   ./backup_enhanced.sh
   ```

2. **Test on local:**
   ```bash
   php artisan test
   ```

3. **Clear caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Run storage link:**
   ```bash
   php artisan storage:link
   ```

5. **Set proper permissions:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chmod -R 775 storage/app/public
   ```

6. **Update .env on VPS:**
   ```bash
   SESSION_ENCRYPT=true
   APP_ENV=production
   APP_DEBUG=false
   ```

7. **Deploy:**
   ```bash
   ./deploy-simple.sh
   ```

---

## ESTIMATED TIME

| Task | Time | Priority |
|------|------|----------|
| File Upload Security | 15 min | 🔴 Critical |
| Authorization Checks | 30 min | 🔴 Critical |
| Security Headers | 5 min | 🔴 Critical |
| Session Encryption | 2 min | 🟠 Medium |
| Rate Limiting | 5 min | 🟠 Medium |
| Testing | 20 min | 🟠 Medium |

**Total:** ~1.5 hours for critical fixes

---

## QUESTIONS?

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify file permissions
4. Ensure storage link exists
5. Clear all caches

**Need help?** Check:
- [Laravel Security Docs](https://laravel.com/docs/11.x/security)
- [File Storage Docs](https://laravel.com/docs/11.x/filesystem)
- [Authorization Docs](https://laravel.com/docs/11.x/authorization)
