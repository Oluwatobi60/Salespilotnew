# System Preferences - Practical Usage Examples

This document demonstrates how to use system preferences throughout your application. All these helper functions and directives are available after implementing the System Preferences feature.

## 1. Controller Examples

### Example 1: Dynamic Pagination

```php
namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function index()
    {
        // Use system-defined items per page instead of hardcoded value
        $users = User::paginate(items_per_page());
        
        return view('users.index', compact('users'));
    }
}
```

### Example 2: Date Formatting

```php
public function showUser($id)
{
    $user = User::findOrFail($id);
    
    // Format dates according to system settings
    $data = [
        'user' => $user,
        'created_date' => format_date($user->created_at),
        'created_time' => format_time($user->created_at),
        'created_full' => format_datetime($user->created_at),
        'current_format' => system_date_format(),
    ];
    
    return view('users.show', $data);
}
```

### Example 3: File Upload Validation

```php
public function uploadFile(Request $request)
{
    $request->validate([
        'file' => [
            'required',
            'file',
            'max:' . max_upload_size(), // Dynamic max size in KB
            function ($attribute, $value, $fail) {
                if (!is_allowed_file($value->getClientOriginalName())) {
                    $allowed = implode(', ', allowed_file_types());
                    $fail("Only these file types are allowed: {$allowed}");
                }
            },
        ],
    ]);
    
    // Process file upload...
    return back()->with('success', 'File uploaded successfully!');
}
```

### Example 4: Using Multiple Settings

```php
public function systemInfo()
{
    $info = [
        'currency' => default_currency(),
        'timezone' => default_timezone(),
        'date_format' => system_date_format(),
        'time_format' => system_time_format(),
        'pagination' => items_per_page(),
        'session_timeout' => session_timeout(),
        'max_upload_mb' => max_upload_size_mb(),
        'allowed_files' => allowed_file_types(),
    ];
    
    return response()->json($info);
}
```

### Example 5: Custom Settings Access

```php
public function customSettings()
{
    // Get any setting by key
    $appName = setting('app_name', 'SalesPilot');
    $supportEmail = setting('support_email', 'support@example.com');
    $maintenanceMode = setting('maintenance_mode', false);
    
    // Update a setting (use with caution)
    update_setting('custom_key', 'custom_value');
    
    return view('settings.custom', compact('appName', 'supportEmail'));
}
```

## 2. Blade Template Examples

### Example 1: Using Blade Directives

```blade
<div class="user-info">
    <h1>Welcome to @setting('app_name', 'SalesPilot')</h1>
    
    <p>Account created: @formatDate($user->created_at)</p>
    <p>Last login: @formatDatetime($user->last_login_at)</p>
    
    <div class="currency">
        Currency: @currency
    </div>
</div>
```

### Example 2: Using View Variables (automatically available)

```blade
<div class="system-info">
    <p>System Currency: {{ $systemCurrency }}</p>
    <p>System Timezone: {{ $systemTimezone }}</p>
    <p>Date Format: {{ $systemDateFormat }}</p>
    <p>Time Format: {{ $systemTimeFormat }}</p>
    <p>Application: {{ $appName }}</p>
</div>
```

### Example 3: Date Formatting in Lists

```blade
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Created Date</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>@formatDate($user->created_at)</td>
                <td>@formatDatetime($user->updated_at)</td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Example 4: File Upload Form with Dynamic Limits

```blade
<form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
    @csrf
    
    <div class="form-group">
        <label>Upload File</label>
        <input type="file" name="file" class="form-control">
        
        <small class="text-muted">
            Max size: {{ max_upload_size_mb() }}MB
            <br>
            Allowed types: {{ implode(', ', allowed_file_types()) }}
        </small>
        
        @error('file')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
    
    <button type="submit" class="btn btn-primary">Upload</button>
</form>
```

### Example 5: Conditional Display Based on Settings

```blade
@if(setting('registration_enabled', true))
    <a href="{{ route('register') }}" class="btn btn-primary">
        Register Now
    </a>
@else
    <p class="text-muted">Registration is currently disabled.</p>
@endif

@if(setting('maintenance_mode', false))
    <div class="alert alert-warning">
        <strong>Maintenance Notice:</strong>
        @setting('maintenance_message', 'System maintenance in progress.')
    </div>
@endif
```

### Example 6: Displaying System Colors

```blade
<style>
    :root {
        --primary-color: {{ primary_color() }};
        --secondary-color: {{ secondary_color() }};
    }
    
    .btn-primary {
        background-color: var(--primary-color);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
    }
</style>
```

### Example 7: Pagination Info

```blade
<div class="pagination-info">
    <p>Showing {{ items_per_page() }} items per page</p>
    
    {{ $users->links() }}
</div>
```

## 3. Model Examples

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    /**
     * Example 1: Accessor with System Date Format
     */
    public function getFormattedCreatedAttribute()
    {
        return format_date($this->created_at);
    }
    
    /**
     * Example 2: Scope with Dynamic Pagination
     */
    public function scopeLatestPaginated($query)
    {
        return $query->latest()->paginate(items_per_page());
    }
    
    /**
     * Example 3: Custom Method Using Settings
     */
    public function canUploadFile($fileSize)
    {
        $maxSize = max_upload_size() * 1024; // Convert KB to bytes
        return $fileSize <= $maxSize;
    }
}
```

## 4. API Controller Examples

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class ApiController extends Controller
{
    /**
     * Return system configuration for frontend
     */
    public function config()
    {
        return response()->json([
            'currency' => default_currency(),
            'timezone' => default_timezone(),
            'date_format' => system_date_format(),
            'time_format' => system_time_format(),
            'datetime_format' => system_datetime_format(),
            'max_upload_mb' => max_upload_size_mb(),
            'allowed_file_types' => allowed_file_types(),
            'items_per_page' => items_per_page(),
            'app' => [
                'name' => app_name(),
                'logo' => app_logo(),
                'favicon' => app_favicon(),
                'primary_color' => primary_color(),
                'secondary_color' => secondary_color(),
            ],
            'contact' => [
                'email' => support_email(),
                'phone' => support_phone(),
            ],
        ]);
    }
    
    /**
     * Return paginated data using system settings
     */
    public function index()
    {
        $data = User::paginate(items_per_page());
        
        return response()->json([
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
            ],
        ]);
    }
}
```

## 5. Validation Rule Examples

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
{
    public function rules()
    {
        // Get file extension from uploaded file
        $allowedTypes = implode(',', allowed_file_types());
        
        return [
            'file' => [
                'required',
                'file',
                'max:' . max_upload_size(), // in KB
                'mimes:' . $allowedTypes,
            ],
            'document' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if (!is_allowed_file($value->getClientOriginalName())) {
                        $fail('This file type is not allowed by system settings.');
                    }
                },
            ],
        ];
    }
}
```

## 6. Livewire Component Examples

```php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersList extends Component
{
    use WithPagination;
    
    protected $paginationTheme = 'bootstrap';
    
    public function render()
    {
        // Use system pagination setting
        $users = User::paginate(items_per_page());
        
        // Pass system settings to view
        return view('livewire.users-list', [
            'users' => $users,
            'dateFormat' => system_date_format(),
            'timeFormat' => system_time_format(),
        ]);
    }
}
```

## 7. Command/Job Examples

```php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExampleCommand extends Command
{
    protected $signature = 'example:run';
    
    public function handle()
    {
        // Set timezone for command execution
        date_default_timezone_set(default_timezone());
        
        $this->info('Running in timezone: ' . default_timezone());
        $this->info('Date format: ' . system_date_format());
        $this->info('Items per page: ' . items_per_page());
        
        // Use settings in your command logic
        $users = User::paginate(items_per_page());
        
        foreach ($users as $user) {
            $this->line(format_datetime($user->created_at));
        }
    }
}
```

## Quick Reference

### Helper Functions

```php
// Currency
default_currency()          // => 'NGN'
default_timezone()          // => 'Africa/Lagos'
system_date_format()        // => 'Y-m-d'
system_time_format()        // => 'H:i:s'
system_datetime_format()    // => 'Y-m-d H:i:s'
items_per_page()            // => 10
session_timeout()           // => 120
max_upload_size()           // => 2048 (KB)
max_upload_size_mb()        // => 2.0 (MB)
allowed_file_types()        // => ['jpg', 'jpeg', 'png', 'pdf']
format_date($date)          // => Formatted date
format_time($time)          // => Formatted time
format_datetime($datetime)  // => Formatted datetime
is_allowed_file($filename)  // => true/false
setting($key, $default)     // => Any setting value
update_setting($key, $val)  // => Update setting
```

### Blade Directives

```blade
@setting('key', 'default')
@formatDate($date)
@formatTime($time)
@formatDatetime($datetime)
@currency
```

### View Variables (automatically available)

```blade
{{ $systemCurrency }}
{{ $systemTimezone }}
{{ $systemDateFormat }}
{{ $systemTimeFormat }}
{{ $appName }}
{{ $primaryColor }}
{{ $secondaryColor }}
```
