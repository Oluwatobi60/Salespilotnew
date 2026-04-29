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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, number, boolean, json, file
            $table->string('group')->default('general'); // general, email, payment, system, appearance, security
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaultSettings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'SalesPilot', 'type' => 'text', 'group' => 'general', 'label' => 'Application Name', 'description' => 'The name of your application'],
            ['key' => 'app_tagline', 'value' => 'Smart Business Management', 'type' => 'text', 'group' => 'general', 'label' => 'Application Tagline', 'description' => 'Short tagline for the application'],
            ['key' => 'support_email', 'value' => 'support@salespilot.com', 'type' => 'text', 'group' => 'general', 'label' => 'Support Email', 'description' => 'Email address for customer support'],
            ['key' => 'support_phone', 'value' => '+234 800 000 0000', 'type' => 'text', 'group' => 'general', 'label' => 'Support Phone', 'description' => 'Phone number for customer support'],
            ['key' => 'timezone', 'value' => 'Africa/Lagos', 'type' => 'text', 'group' => 'general', 'label' => 'Default Timezone', 'description' => 'Application timezone'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'label' => 'Maintenance Mode', 'description' => 'Enable to put the application in maintenance mode'],
            ['key' => 'maintenance_message', 'value' => 'We are currently performing scheduled maintenance. Please check back soon.', 'type' => 'text', 'group' => 'general', 'label' => 'Maintenance Message', 'description' => 'Message to display during maintenance'],
            
            // Email Settings
            ['key' => 'mail_driver', 'value' => 'smtp', 'type' => 'text', 'group' => 'email', 'label' => 'Mail Driver', 'description' => 'Email service driver (smtp, mailgun, ses, etc.)'],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'type' => 'text', 'group' => 'email', 'label' => 'SMTP Host', 'description' => 'SMTP server hostname'],
            ['key' => 'mail_port', 'value' => '587', 'type' => 'number', 'group' => 'email', 'label' => 'SMTP Port', 'description' => 'SMTP server port'],
            ['key' => 'mail_username', 'value' => '', 'type' => 'text', 'group' => 'email', 'label' => 'SMTP Username', 'description' => 'SMTP authentication username'],
            ['key' => 'mail_password', 'value' => '', 'type' => 'password', 'group' => 'email', 'label' => 'SMTP Password', 'description' => 'SMTP authentication password'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'type' => 'text', 'group' => 'email', 'label' => 'Mail Encryption', 'description' => 'Email encryption (tls, ssl, or null)'],
            ['key' => 'mail_from_address', 'value' => 'noreply@salespilot.com', 'type' => 'text', 'group' => 'email', 'label' => 'From Email Address', 'description' => 'Default sender email address'],
            ['key' => 'mail_from_name', 'value' => 'SalesPilot', 'type' => 'text', 'group' => 'email', 'label' => 'From Name', 'description' => 'Default sender name'],
            
            // Payment Settings
            ['key' => 'paystack_public_key', 'value' => '', 'type' => 'text', 'group' => 'payment', 'label' => 'Paystack Public Key', 'description' => 'Paystack public API key'],
            ['key' => 'paystack_secret_key', 'value' => '', 'type' => 'password', 'group' => 'payment', 'label' => 'Paystack Secret Key', 'description' => 'Paystack secret API key'],
            ['key' => 'paystack_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'payment', 'label' => 'Enable Paystack', 'description' => 'Enable Paystack payment gateway'],
            ['key' => 'bank_transfer_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'payment', 'label' => 'Enable Bank Transfer', 'description' => 'Enable bank transfer payment method'],
            ['key' => 'bank_name', 'value' => 'GTBank', 'type' => 'text', 'group' => 'payment', 'label' => 'Bank Name', 'description' => 'Bank name for transfer payments'],
            ['key' => 'bank_account_number', 'value' => '0123456789', 'type' => 'text', 'group' => 'payment', 'label' => 'Account Number', 'description' => 'Bank account number'],
            ['key' => 'bank_account_name', 'value' => 'SalesPilot Technologies', 'type' => 'text', 'group' => 'payment', 'label' => 'Account Name', 'description' => 'Bank account name'],
            ['key' => 'currency', 'value' => 'NGN', 'type' => 'text', 'group' => 'payment', 'label' => 'Currency', 'description' => 'Default currency code'],
            ['key' => 'currency_symbol', 'value' => '₦', 'type' => 'text', 'group' => 'payment', 'label' => 'Currency Symbol', 'description' => 'Currency symbol'],
            
            // System Settings
            ['key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'system', 'label' => 'Enable Registration', 'description' => 'Allow new user registrations'],
            ['key' => 'email_verification_required', 'value' => '1', 'type' => 'boolean', 'group' => 'system', 'label' => 'Require Email Verification', 'description' => 'Users must verify email before access'],
            ['key' => 'default_trial_days', 'value' => '7', 'type' => 'number', 'group' => 'system', 'label' => 'Default Trial Days', 'description' => 'Default free trial period in days'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'number', 'group' => 'system', 'label' => 'Session Lifetime', 'description' => 'Session timeout in minutes'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'number', 'group' => 'system', 'label' => 'Max Login Attempts', 'description' => 'Maximum failed login attempts before lockout'],
            ['key' => 'auto_backup_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'system', 'label' => 'Enable Auto Backup', 'description' => 'Automatically backup database'],
            ['key' => 'backup_frequency', 'value' => 'daily', 'type' => 'text', 'group' => 'system', 'label' => 'Backup Frequency', 'description' => 'How often to backup (daily, weekly, monthly)'],
            
            // Appearance Settings
            ['key' => 'logo_url', 'value' => '', 'type' => 'file', 'group' => 'appearance', 'label' => 'Application Logo', 'description' => 'Main application logo'],
            ['key' => 'favicon_url', 'value' => '', 'type' => 'file', 'group' => 'appearance', 'label' => 'Favicon', 'description' => 'Browser favicon icon'],
            ['key' => 'primary_color', 'value' => '#667eea', 'type' => 'color', 'group' => 'appearance', 'label' => 'Primary Color', 'description' => 'Main brand color'],
            ['key' => 'secondary_color', 'value' => '#764ba2', 'type' => 'color', 'group' => 'appearance', 'label' => 'Secondary Color', 'description' => 'Secondary brand color'],
            ['key' => 'footer_text', 'value' => '© 2026 SalesPilot. All rights reserved.', 'type' => 'text', 'group' => 'appearance', 'label' => 'Footer Text', 'description' => 'Footer copyright text'],
            
            // Security Settings
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'number', 'group' => 'security', 'label' => 'Min Password Length', 'description' => 'Minimum password length requirement'],
            ['key' => 'require_strong_password', 'value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'Require Strong Password', 'description' => 'Enforce strong password requirements'],
            ['key' => 'two_factor_enabled', 'value' => '0', 'type' => 'boolean', 'group' => 'security', 'label' => 'Enable 2FA', 'description' => 'Enable two-factor authentication'],
            ['key' => 'ip_whitelist', 'value' => '', 'type' => 'text', 'group' => 'security', 'label' => 'IP Whitelist', 'description' => 'Comma-separated list of allowed IPs (leave empty for all)'],
            ['key' => 'force_https', 'value' => '1', 'type' => 'boolean', 'group' => 'security', 'label' => 'Force HTTPS', 'description' => 'Force HTTPS connections'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('app_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
