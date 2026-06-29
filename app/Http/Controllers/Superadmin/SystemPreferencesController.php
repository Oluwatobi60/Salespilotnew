<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch\Branch;
use App\Models\Staffs;
use App\Models\User;
use App\Models\Brm;
use App\Models\SubscriptionPlan;
use App\Models\AppSetting;

class SystemPreferencesController extends Controller
{
    public function index()
    {
        // Get system-wide statistics
        $totalBusinesses = User::where('role', 'manager')
            ->whereNull('addby')
            ->count();
        $totalBranches = Branch::count();
        $totalStaff = Staffs::count();
        $totalBrms = Brm::count();
        $activePlans = SubscriptionPlan::where('is_active', true)->count();

        // Get all branches with their business information
        $branches = Branch::with(['manager', 'staffMembers'])
            ->orderBy('created_at', 'desc')
            ->paginate(items_per_page());

        // Get all staff members
        $staffs = Staffs::with('branches')
            ->orderBy('created_at', 'desc')
            ->paginate(items_per_page());

        // Get all BRMs
        $brms = Brm::withCount('customers')
            ->orderBy('created_at', 'desc')
            ->paginate(items_per_page());

        // Get system preferences from AppSetting
        $systemPreferences = [
            'default_currency' => AppSetting::get('default_currency', 'NGN'),
            'default_timezone' => AppSetting::get('default_timezone', 'Africa/Lagos'),
            'date_format' => AppSetting::get('date_format', 'Y-m-d'),
            'time_format' => AppSetting::get('time_format', 'H:i:s'),
            'items_per_page' => AppSetting::get('items_per_page', '10'),
            'session_timeout' => AppSetting::get('session_timeout', '120'),
            'max_upload_size' => AppSetting::get('max_upload_size', '2048'),
            'allowed_file_types' => AppSetting::get('allowed_file_types', 'jpg,jpeg,png,pdf'),
        ];

        // Get stack version information
        $stackVersions = [
            'php' => phpversion(),
            'laravel' => app()->version(),
            'mysql' => DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
        ];

        return view('superadmin.settings.system_preferences', compact(
            'totalBusinesses',
            'totalBranches',
            'totalStaff',
            'totalBrms',
            'activePlans',
            'branches',
            'staffs',
            'brms',
            'systemPreferences',
            'stackVersions'
        ));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'default_currency' => 'required|string|max:10',
            'default_timezone' => 'required|string|max:255',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'items_per_page' => 'required|integer|min:5|max:100',
            'session_timeout' => 'required|integer|min:5|max:1440',
            'max_upload_size' => 'required|integer|min:1024|max:10240',
            'allowed_file_types' => 'required|string',
        ]);

        try {
            // Update or create system preferences
            foreach ($validatedData as $key => $value) {
                // Try to update existing setting
                $setting = AppSetting::where('key', $key)->first();

                if ($setting) {
                    // Update existing setting
                    $setting->value = $value;
                    $setting->save();
                } else {
                    // Create new setting if it doesn't exist
                    AppSetting::create([
                        'key' => $key,
                        'value' => $value,
                        'type' => is_numeric($value) ? 'number' : 'text',
                        'group' => 'system',
                        'label' => ucwords(str_replace('_', ' ', $key)),
                        'description' => 'System preference for ' . str_replace('_', ' ', $key),
                    ]);
                }

                // Clear cache for this setting
                \Illuminate\Support\Facades\Cache::forget("app_setting_{$key}");
            }

            return redirect()
                ->route('superadmin.system-preferences')
                ->with('success', 'System preferences updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update system preferences: ' . $e->getMessage());
        }
    }
}
