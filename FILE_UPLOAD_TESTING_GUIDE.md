# File Upload Security Testing Guide

## Quick Test Commands

### 1. Create Test Files

#### PHP File (Should be REJECTED)
```bash
echo "<?php echo 'test'; ?>" > test.php
```

#### EXE File (Should be REJECTED)  
```bash
# On Windows PowerShell
New-Item test.exe -ItemType File
```

#### Valid Image (Should be ACCEPTED)
```bash
# Download a small valid JPEG
curl https://via.placeholder.com/150 -o test.jpg
```

#### Large Image >2MB (Should be REJECTED)
```bash
# Download a large image
curl https://via.placeholder.com/5000x5000.jpg -o large.jpg
```

## Testing Steps

### Step 1: Test Valid Image Upload
1. Go to Manager → Add New Item
2. Fill in required fields:
   - Item Name: "Security Test Item"
   - Item Code: "SEC001"
   - Category: Select any
   - Cost Price: 100
   - Selling Price: 150
   - Pricing Type: Fixed
3. Upload `test.jpg` (valid JPEG image)
4. **Expected**: ✅ Item created successfully, image displays in sell product page

### Step 2: Test PHP File Upload (CRITICAL)
1. Try to upload `test.php` using same form
2. **Expected**: ❌ Validation error: "The item image must be a file of type: jpeg, png, jpg, gif, webp."
3. **If it uploads successfully**: 🚨 SECURITY FIX FAILED - DO NOT DEPLOY

### Step 3: Test EXE File Upload
1. Try to upload `test.exe` using same form
2. **Expected**: ❌ Validation error: "The item image must be a file of type: jpeg, png, jpg, gif, webp."

### Step 4: Test Large File Upload
1. Try to upload `large.jpg` (>2MB)
2. **Expected**: ❌ Validation error: "The item image may not be greater than 2048 kilobytes."

### Step 5: Test Staff Photo Upload
1. Go to Manager → Staff Management → Add Staff
2. Upload valid image: ✅ Should work
3. Upload .php file: ❌ Should be rejected
4. **Expected**: Same validation as items

### Step 6: Test Business Logo Upload
1. Go to Manager → Add Manager (multi-manager feature)
2. Upload valid image: ✅ Should work (up to 5MB)
3. Upload .php file: ❌ Should be rejected

### Step 7: Test Image Display
1. Go to Manager → Sell Product
2. Verify newly uploaded images display correctly
3. Verify old images (from `public/uploads/`) still display
4. **Check**: Images use path like `/storage/item_images/randomhash.jpg`

### Step 8: Test Backward Compatibility
1. Check database for old items with paths like `uploads/item_images/old_photo.jpg`
2. View these items on sell product page
3. **Expected**: Old images still display correctly using `asset('uploads/...')`

## Automated Testing Script

```php
<?php
// tests/Feature/FileUploadSecurityTest.php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    public function test_rejects_php_file_upload()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('malicious.php', 100, 'text/plain');
        
        $response = $this->actingAs($this->createManager())
            ->post(route('manager.standarditem.store'), [
                'item_name' => 'Test',
                'item_code' => 'TEST001',
                'category_id' => 1,
                'cost_price' => 100,
                'selling_price' => 150,
                'pricing_type' => 'fixed',
                'item_image' => $file,
            ]);
        
        $response->assertSessionHasErrors('item_image');
        Storage::disk('public')->assertMissing('item_images/malicious.php');
    }
    
    public function test_accepts_valid_image()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->image('product.jpg');
        
        $response = $this->actingAs($this->createManager())
            ->post(route('manager.standarditem.store'), [
                'item_name' => 'Test',
                'item_code' => 'TEST001',
                'category_id' => 1,
                'cost_price' => 100,
                'selling_price' => 150,
                'pricing_type' => 'fixed',
                'item_image' => $file,
            ]);
        
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('standard_items', ['item_name' => 'Test']);
    }
    
    public function test_rejects_large_file()
    {
        Storage::fake('public');
        
        $file = UploadedFile::fake()->create('large.jpg', 3000); // 3MB
        
        $response = $this->actingAs($this->createManager())
            ->post(route('manager.standarditem.store'), [
                'item_name' => 'Test',
                'item_code' => 'TEST001',
                'category_id' => 1,
                'cost_price' => 100,
                'selling_price' => 150,
                'pricing_type' => 'fixed',
                'item_image' => $file,
            ]);
        
        $response->assertSessionHasErrors('item_image');
    }
}
```

Run tests:
```bash
php artisan test --filter FileUploadSecurityTest
```

## Manual Browser Testing Checklist

- [ ] ✅ Valid JPEG uploads successfully
- [ ] ✅ Valid PNG uploads successfully
- [ ] ✅ Valid GIF uploads successfully
- [ ] ✅ Valid WEBP uploads successfully
- [ ] ❌ PHP file is rejected with error message
- [ ] ❌ EXE file is rejected with error message
- [ ] ❌ BAT file is rejected with error message
- [ ] ❌ JS file is rejected with error message
- [ ] ❌ 3MB file is rejected with error message
- [ ] ✅ New uploads display correctly in sell product page
- [ ] ✅ Old uploads (from uploads/) still display correctly
- [ ] ✅ Staff photos upload with same security
- [ ] ✅ Business logos upload with same security
- [ ] ✅ Update item image works securely
- [ ] ✅ Update staff photo works securely

## VPS Deployment Checklist

After deploying to VPS:

```bash
# 1. Ensure storage link exists
php artisan storage:link

# 2. Set correct permissions
chmod -R 775 storage/app/public
chmod -R 775 storage/app/public/item_images
chmod -R 775 storage/app/public/staff_photos
chmod -R 775 storage/app/public/business_logos

# 3. Verify web server can write
sudo chown -R www-data:www-data storage/app/public
# OR for nginx
sudo chown -R nginx:nginx storage/app/public

# 4. Test upload from browser
# Follow manual testing steps above

# 5. Check log for errors
tail -f storage/logs/laravel.log
```

## Security Verification

### Check 1: File Extension
```bash
# Try to upload with .php extension
# Expected: Rejected by Laravel validation
```

### Check 2: MIME Type
```bash
# Try to rename .php to .jpg (fake extension)
# Expected: Still rejected (Laravel checks actual MIME type)
```

### Check 3: File Size
```bash
# Upload >2MB image
# Expected: Rejected with size error
```

### Check 4: Random Filename
```bash
# Check storage/app/public/item_images/
# Expected: Files have random hashed names, NOT user-provided names
ls -la storage/app/public/item_images/
# Should see: randomhash123456.jpg (not original_filename.jpg)
```

### Check 5: Storage Location
```bash
# Verify files NOT in public/uploads (old insecure location)
ls public/uploads/item_images/
# Should be empty for NEW uploads

# Verify files ARE in storage/app/public
ls storage/app/public/item_images/
# Should contain new uploads with random names
```

## What to Look For (PASS/FAIL)

### ✅ PASS Indicators
- PHP files are rejected
- EXE files are rejected
- Large files are rejected
- Valid images upload successfully
- Images display correctly
- Filenames in storage are random hashes
- Old images still work (backward compatibility)
- No errors in Laravel log

### ❌ FAIL Indicators
- PHP files can be uploaded
- Files stored with user-provided names
- Files stored in public/uploads (old location)
- Validation errors not showing
- Images don't display after upload
- Old images broken

## Rollback Plan

If security fix breaks image uploads:

```bash
# 1. Revert controllers
git diff HEAD~1 app/Http/Controllers/Manager/StandardItemController.php
git checkout HEAD~1 app/Http/Controllers/Manager/StandardItemController.php

# 2. Revert views
git checkout HEAD~1 resources/views/manager/sell/sell_product.blade.php

# 3. Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# 4. Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

## Support

If issues occur:
1. Check Laravel log: `storage/logs/laravel.log`
2. Check web server error log: `/var/log/nginx/error.log`
3. Verify permissions: `ls -la storage/app/public`
4. Test storage link: `ls -la public/storage`
5. Review validation errors in browser network tab
