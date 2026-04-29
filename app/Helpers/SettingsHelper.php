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
