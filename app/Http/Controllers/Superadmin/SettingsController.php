<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard with widgets
     */
    public function index()
    {
        // Clear settings cache to ensure fresh data
        AppSetting::clearCache();

        $groups = [
            'general' => AppSetting::getByGroup('general'),
            'email' => AppSetting::getByGroup('email'),
            'payment' => AppSetting::getByGroup('payment'),
            'system' => AppSetting::getByGroup('system'),
            'appearance' => AppSetting::getByGroup('appearance'),
            'security' => AppSetting::getByGroup('security'),
        ];

        return response()
            ->view('superadmin.settings.index', compact('groups'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'nullable|array',
            'settings.*' => 'nullable',
        ]);

        // Get submitted settings array
        $submittedSettings = $request->input('settings', []);

        // Log the request data for debugging
        Log::info('Settings Update Request', [
            'submitted_settings' => $submittedSettings,
            'has_auto_backup_in_array' => isset($submittedSettings['auto_backup_enabled']),
        ]);

        try {
            DB::beginTransaction();

            // Get all boolean settings to handle unchecked checkboxes
            $booleanSettings = AppSetting::where('type', 'boolean')->get();

            // Update all boolean settings (unchecked checkboxes won't be in the request)
            foreach ($booleanSettings as $setting) {
                // Check if the key exists in the submitted settings array
                $isChecked = isset($submittedSettings[$setting->key]);

                Log::info("Updating boolean setting: {$setting->key}", [
                    'is_checked' => $isChecked,
                    'old_value' => $setting->value,
                    'new_value' => $isChecked ? '1' : '0',
                ]);

                // Update directly in database to avoid cache issues
                $setting->value = $isChecked ? '1' : '0';
                $setting->save();

                // Clear cache immediately
                Cache::forget("app_setting_{$setting->key}");
            }

            // Process all other submitted settings
            if (!empty($submittedSettings)) {
                foreach ($submittedSettings as $key => $value) {
                    $setting = AppSetting::where('key', $key)->first();

                    if (!$setting || $setting->type === 'boolean') {
                        // Skip if not found or already processed as boolean
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

                    // Update the setting
                    $setting->value = $value;
                    $setting->save();

                    // Clear cache for this setting
                    Cache::forget("app_setting_{$key}");
                }
            }

            DB::commit();

            // Clear all settings cache after commit
            Cache::flush();

            // Clear application cache for immediate effect
            Artisan::call('config:clear');

            Log::info('Settings updated successfully');

            return back()->with('success', 'Settings updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Settings update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
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
     * Update individual toggle setting via AJAX
     */
    public function updateToggle(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required|in:0,1',
        ]);

        try {
            $setting = AppSetting::where('key', $validated['key'])->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found'
                ], 404);
            }

            if ($setting->type !== 'boolean') {
                return response()->json([
                    'success' => false,
                    'message' => 'This setting is not a boolean type'
                ], 400);
            }

            // Update the setting
            $setting->value = $validated['value'];
            $setting->save();

            // Clear cache for this setting
            Cache::forget("app_setting_{$validated['key']}");

            // Clear application config cache
            Artisan::call('config:clear');

            Log::info("Toggle setting updated", [
                'key' => $validated['key'],
                'value' => $validated['value'],
                'label' => $setting->label
            ]);

            return response()->json([
                'success' => true,
                'message' => $setting->label . ' updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle update failed', [
                'key' => $validated['key'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run database backup
     */
    public function runBackup()
    {
        try {
            // Run the custom backup command
            Artisan::call('backup:database');
            $output = Artisan::output();

            // Check if backup was successful
            if (str_contains($output, 'successfully')) {
                // Try to find the location in the output
                $filename = null;
                if (preg_match('/Location:\s*(.*)/', $output, $matches)) {
                    $filepath = trim($matches[1]);
                    $filename = basename($filepath);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Database backup completed successfully!',
                    'download_url' => $filename ? route('superadmin.settings.download-backup', ['filename' => $filename]) : null
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed. Please check server logs.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a database backup file
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (!file_exists($path)) {
            abort(404, 'Backup file not found.');
        }
        return response()->download($path);
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
