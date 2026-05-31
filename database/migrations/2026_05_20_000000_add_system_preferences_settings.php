<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            ['key' => 'default_currency', 'value' => 'NGN', 'type' => 'text', 'group' => 'system', 'label' => 'Default Currency', 'description' => 'Default system currency code'],
            ['key' => 'default_timezone', 'value' => 'Africa/Lagos', 'type' => 'text', 'group' => 'system', 'label' => 'Default Timezone', 'description' => 'Default system timezone'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'text', 'group' => 'system', 'label' => 'Date Format', 'description' => 'Default date display format'],
            ['key' => 'time_format', 'value' => 'H:i:s', 'type' => 'text', 'group' => 'system', 'label' => 'Time Format', 'description' => 'Default time display format'],
            ['key' => 'items_per_page', 'value' => '10', 'type' => 'number', 'group' => 'system', 'label' => 'Items Per Page', 'description' => 'Default pagination items per page'],
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'number', 'group' => 'system', 'label' => 'Session Timeout', 'description' => 'Session timeout in minutes'],
            ['key' => 'max_upload_size', 'value' => '2048', 'type' => 'number', 'group' => 'system', 'label' => 'Max Upload Size', 'description' => 'Maximum file upload size in KB'],
            ['key' => 'allowed_file_types', 'value' => 'jpg,jpeg,png,pdf', 'type' => 'text', 'group' => 'system', 'label' => 'Allowed File Types', 'description' => 'Comma-separated list of allowed file extensions'],
        ];

        foreach ($settings as $setting) {
            // Check if setting already exists
            $exists = DB::table('app_settings')->where('key', $setting['key'])->exists();

            if (!$exists) {
                DB::table('app_settings')->insert(array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'default_currency',
            'default_timezone',
            'date_format',
            'time_format',
            'items_per_page',
            'session_timeout',
            'max_upload_size',
            'allowed_file_types',
        ];

        DB::table('app_settings')->whereIn('key', $keys)->delete();
    }
};
