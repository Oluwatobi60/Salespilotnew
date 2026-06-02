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
        DB::table('subscription_features')->insertOrIgnore([
            'name' => 'Edit Items In Subscription Features',
            'slug' => 'manager_edit_items_features',
            'description' => 'Allow managers added by the business creator/owner to edit or delete subscription feature items when enabled in the superadmin module.',
            'role' => 'manager',
            'category' => 'user_management',
            'is_active' => true,
            'sort_order' => 999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subscription_features')->where('slug', 'manager_edit_items_features')->delete();
    }
};
