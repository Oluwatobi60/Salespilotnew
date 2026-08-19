<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if settings table exists
        if (Schema::hasTable('app_settings')) {
            // Check if key already exists
            $exists = DB::table('app_settings')->where('key', 'gemini_api_key')->exists();
            if (!$exists) {
                DB::table('app_settings')->insert([
                    'key' => 'gemini_api_key',
                    'value' => '',
                    'type' => 'password',
                    'group' => 'system',
                    'label' => 'Gemini API Key',
                    'description' => 'Google Gemini API key for AI features (category suggestion, product copywriting, price recommendation)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('app_settings')) {
            DB::table('app_settings')->where('key', 'gemini_api_key')->delete();
        }
    }
};
