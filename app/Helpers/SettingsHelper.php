<?php

/**
 * Settings Helper Functions
 *
 * These functions provide easy access to application settings
 * configured through the superadmin panel.
 */

use App\Models\AppSetting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return AppSetting::get($key, $default);
    }
}

if (!function_exists('settings')) {
    /**
     * Get settings by group
     *
     * @param string|null $group Group name (null for all)
     * @return \Illuminate\Support\Collection|array
     */
    function settings(?string $group = null)
    {
        if ($group) {
            return AppSetting::getByGroup($group);
        }

        return AppSetting::all();
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update a setting value
     *
     * @param string $key Setting key
     * @param mixed $value New value
     * @return bool
     */
    function update_setting(string $key, $value): bool
    {
        return AppSetting::set($key, $value);
    }
}

if (!function_exists('app_name')) {
    /**
     * Get the application name
     *
     * @return string
     */
    function app_name(): string
    {
        return setting('app_name', config('app.name', 'SalesPilot'));
    }
}

if (!function_exists('app_logo')) {
    /**
     * Get the application logo URL
     *
     * @return string|null
     */
    function app_logo(): ?string
    {
        $logo = setting('logo_url');
        return $logo ? asset('storage/' . $logo) : null;
    }
}

if (!function_exists('app_favicon')) {
    /**
     * Get the application favicon URL
     *
     * @return string
     */
    function app_favicon(): string
    {
        $favicon = setting('favicon_url');
        return $favicon ? asset('storage/' . $favicon) : asset('manager_asset/images/favicon.png');
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Check if application is in maintenance mode
     *
     * @return bool
     */
    function is_maintenance_mode(): bool
    {
        return (bool) setting('maintenance_mode', false);
    }
}

if (!function_exists('support_email')) {
    /**
     * Get support email address
     *
     * @return string
     */
    function support_email(): string
    {
        return setting('support_email', 'support@salespilot.com');
    }
}

if (!function_exists('support_phone')) {
    /**
     * Get support phone number
     *
     * @return string
     */
    function support_phone(): string
    {
        return setting('support_phone', '+234 800 000 0000');
    }
}

if (!function_exists('primary_color')) {
    /**
     * Get primary brand color
     *
     * @return string
     */
    function primary_color(): string
    {
        return setting('primary_color', '#667eea');
    }
}

if (!function_exists('secondary_color')) {
    /**
     * Get secondary brand color
     *
     * @return string
     */
    function secondary_color(): string
    {
        return setting('secondary_color', '#764ba2');
    }
}

if (!function_exists('is_registration_enabled')) {
    /**
     * Check if new user registration is enabled
     *
     * @return bool
     */
    function is_registration_enabled(): bool
    {
        return (bool) setting('registration_enabled', true);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @return string
     */
    function currency_symbol(): string
    {
        return setting('currency_symbol', '₦');
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get currency code
     *
     * @return string
     */
    function currency_code(): string
    {
        return setting('currency', 'NGN');
    }
}

if (!function_exists('default_currency')) {
    /**
     * Get default system currency
     *
     * @return string
     */
    function default_currency(): string
    {
        return setting('default_currency', 'NGN');
    }
}

if (!function_exists('default_timezone')) {
    /**
     * Get default system timezone
     *
     * @return string
     */
    function default_timezone(): string
    {
        return setting('default_timezone', 'Africa/Lagos');
    }
}

if (!function_exists('system_date_format')) {
    /**
     * Get system date format
     *
     * @return string
     */
    function system_date_format(): string
    {
        return setting('date_format', 'Y-m-d');
    }
}

if (!function_exists('system_time_format')) {
    /**
     * Get system time format
     *
     * @return string
     */
    function system_time_format(): string
    {
        return setting('time_format', 'H:i:s');
    }
}

if (!function_exists('system_datetime_format')) {
    /**
     * Get system datetime format
     *
     * @return string
     */
    function system_datetime_format(): string
    {
        return system_date_format() . ' ' . system_time_format();
    }
}

if (!function_exists('items_per_page')) {
    /**
     * Get default items per page for pagination
     *
     * @return int
     */
    function items_per_page(): int
    {
        return (int) setting('items_per_page', 10);
    }
}

if (!function_exists('session_timeout')) {
    /**
     * Get session timeout in minutes
     *
     * @return int
     */
    function session_timeout(): int
    {
        return (int) setting('session_timeout', 120);
    }
}

if (!function_exists('max_upload_size')) {
    /**
     * Get maximum upload size in KB
     *
     * @return int
     */
    function max_upload_size(): int
    {
        return (int) setting('max_upload_size', 2048);
    }
}

if (!function_exists('max_upload_size_mb')) {
    /**
     * Get maximum upload size in MB
     *
     * @return float
     */
    function max_upload_size_mb(): float
    {
        return max_upload_size() / 1024;
    }
}

if (!function_exists('allowed_file_types')) {
    /**
     * Get allowed file types as array
     *
     * @return array
     */
    function allowed_file_types(): array
    {
        $types = setting('allowed_file_types', 'jpg,jpeg,png,pdf');
        return array_map('trim', explode(',', $types));
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date according to system settings
     *
     * @param mixed $date Date string or Carbon instance
     * @return string
     */
    function format_date($date): string
    {
        if (!$date) return '';

        try {
            $carbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);
            return $carbon->format(system_date_format());
        } catch (\Exception $e) {
            return (string) $date;
        }
    }
}

if (!function_exists('format_time')) {
    /**
     * Format a time according to system settings
     *
     * @param mixed $time Time string or Carbon instance
     * @return string
     */
    function format_time($time): string
    {
        if (!$time) return '';

        try {
            $carbon = $time instanceof \Carbon\Carbon ? $time : \Carbon\Carbon::parse($time);
            return $carbon->format(system_time_format());
        } catch (\Exception $e) {
            return (string) $time;
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format a datetime according to system settings
     *
     * @param mixed $datetime Datetime string or Carbon instance
     * @return string
     */
    function format_datetime($datetime): string
    {
        if (!$datetime) return '';

        try {
            $carbon = $datetime instanceof \Carbon\Carbon ? $datetime : \Carbon\Carbon::parse($datetime);
            return $carbon->format(system_datetime_format());
        } catch (\Exception $e) {
            return (string) $datetime;
        }
    }
}

if (!function_exists('is_allowed_file')) {
    /**
     * Check if a file extension is allowed
     *
     * @param string $filename Filename or extension
     * @return bool
     */
    function is_allowed_file(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, allowed_file_types());
    }
}

if (!function_exists('user_has_feature')) {
    /**
     * Check if the authenticated user's subscription plan has a specific feature
     *
     * @param string $featureSlug The feature slug to check
     * @param \App\Models\User|\App\Models\Staffs|null $user Optional user (defaults to authenticated user)
     * @return bool
     */
    function user_has_feature(string $featureSlug, $user = null): bool
    {
        // Get user (passed or authenticated)
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return false;
        }

        // If this is a staff user, get the parent user's subscription
        if ($user instanceof \App\Models\Staffs) {
            // Staff users inherit features from their manager/business creator
            $parentUser = \App\Models\User::where('email', $user->manager_email)->first();

            if (!$parentUser) {
                return false;
            }

            // Get parent user's subscription
            $subscription = $parentUser->currentSubscription()->first();
        } else {
            // Regular user - get their own subscription
            $subscription = $user->currentSubscription()->first();
        }

        if (!$subscription || !$subscription->subscriptionPlan) {
            return false;
        }

        // Check if plan has the feature
        return $subscription->subscriptionPlan->hasFeature($featureSlug);
    }
}

if (!function_exists('plan_has_feature')) {
    /**
     * Check if a subscription plan has a specific feature
     *
     * @param \App\Models\SubscriptionPlan|int $plan Plan model or ID
     * @param string $featureSlug The feature slug to check
     * @return bool
     */
    function plan_has_feature($plan, string $featureSlug): bool
    {
        if (is_numeric($plan)) {
            $plan = \App\Models\SubscriptionPlan::find($plan);
        }

        if (!$plan) {
            return false;
        }

        return $plan->hasFeature($featureSlug);
    }
}

if (!function_exists('user_subscription_features')) {
    /**
     * Get all enabled features for the authenticated user's subscription
     *
     * @param \App\Models\User|null $user Optional user (defaults to authenticated user)
     * @return array Array of feature slugs
     */
    function user_subscription_features($user = null): array
    {
        // Get user (passed or authenticated)
        if (!$user) {
            $user = auth()->user();
        }

        if (!$user) {
            return [];
        }

        // Get user's active subscription
        $subscription = $user->currentSubscription()->first();

        if (!$subscription || !$subscription->subscriptionPlan) {
            return [];
        }

        return $subscription->subscriptionPlan->features ?? [];
    }
}
