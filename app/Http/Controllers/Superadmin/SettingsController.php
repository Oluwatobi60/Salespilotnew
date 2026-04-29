<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard with widgets
     */
    public function index()
    {
        $groups = [
            'general' => AppSetting::getByGroup('general'),
            'email' => AppSetting::getByGroup('email'),
            'payment' => AppSetting::getByGroup('payment'),
            'system' => AppSetting::getByGroup('system'),
            'appearance' => AppSetting::getByGroup('appearance'),
            'security' => AppSetting::getByGroup('security'),
        ];

        return view('superadmin.settings.index', compact('groups'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = AppSetting::where('key', $key)->first();
            
            if (!$setting) {
                continue;
            }

            // Handle file uploads
            if ($setting->type === 'file' && $request->hasFile("settings.{$key}")) {
                $file = $request->file("settings.{$key}");
                $path = $file->store('settings', 'public');
                $value = $path;

                // Delete old file if exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
            }

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = $request->has("settings.{$key}") ? '1' : '0';
            }

            $setting->value = $value;
            $setting->save();
        }

        // Clear all settings cache
        AppSetting::clearCache();

        // Clear application cache for immediate effect
        Artisan::call('config:clear');
        Cache::flush();

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            // Send test email
            Mail::raw('This is a test email from ' . app_name() . ' settings configuration.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                    ->subject('Test Email - ' . app_name());
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $validated['test_email']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            AppSetting::clearCache();

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run database backup
     */
    public function runBackup()
    {
        try {
            Artisan::call('backup:run');
            return response()->json([
                'success' => true,
                'message' => 'Database backup completed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance()
    {
        try {
            $currentMode = AppSetting::get('maintenance_mode', false);
            $newMode = !$currentMode;

            if ($newMode) {
                // Set the setting BEFORE enabling maintenance mode
                AppSetting::set('maintenance_mode', true);
                Artisan::call('down', ['--secret' => 'superadmin-bypass']);
                $message = 'Maintenance mode enabled. Use /superadmin-bypass to access.';
            } else {
                Artisan::call('up');
                AppSetting::set('maintenance_mode', false);
                $message = 'Maintenance mode disabled. Application is now live.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'mode' => $newMode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }
}
