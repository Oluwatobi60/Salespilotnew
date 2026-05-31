<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Apply system preferences globally
        $this->applySystemPreferences();

        // Share system settings with all views
        $this->shareViewVariables();

        // Register Blade directives for settings
        $this->registerBladeDirectives();
    }

    /**
     * Apply system preferences to Laravel configuration
     */
    protected function applySystemPreferences(): void
    {
        try {
            // Check if app_settings table exists (avoid errors during fresh migrations)
            if (!\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                return;
            }

            // Apply timezone setting
            $timezone = AppSetting::get('default_timezone');
            if ($timezone) {
                Config::set('app.timezone', $timezone);
                date_default_timezone_set($timezone);
            }

            // Apply session timeout setting
            $sessionTimeout = AppSetting::get('session_timeout');
            if ($sessionTimeout) {
                Config::set('session.lifetime', (int) $sessionTimeout);
            }

            // Apply currency settings
            $currency = AppSetting::get('default_currency');
            if ($currency) {
                Config::set('app.currency', $currency);
            }

        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application
            // This can happen during initial setup or migrations
            \Illuminate\Support\Facades\Log::debug('Failed to apply system preferences: ' . $e->getMessage());
        }
    }

    /**
     * Share system settings with all views
     */
    protected function shareViewVariables(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                return;
            }

            View::composer('*', function ($view) {
                $view->with([
                    'systemCurrency' => default_currency(),
                    'systemTimezone' => default_timezone(),
                    'systemDateFormat' => system_date_format(),
                    'systemTimeFormat' => system_time_format(),
                    'appName' => app_name(),
                    'primaryColor' => primary_color(),
                    'secondaryColor' => secondary_color(),
                ]);
            });

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::debug('Failed to share view variables: ' . $e->getMessage());
        }
    }

    /**
     * Register custom Blade directives for settings
     */
    protected function registerBladeDirectives(): void
    {
        // @setting('key', 'default') - Get a setting value
        Blade::directive('setting', function ($expression) {
            return "<?php echo setting({$expression}); ?>";
        });

        // @formatDate($date) - Format date according to system settings
        Blade::directive('formatDate', function ($expression) {
            return "<?php echo format_date({$expression}); ?>";
        });

        // @formatTime($time) - Format time according to system settings
        Blade::directive('formatTime', function ($expression) {
            return "<?php echo format_time({$expression}); ?>";
        });

        // @formatDatetime($datetime) - Format datetime according to system settings
        Blade::directive('formatDatetime', function ($expression) {
            return "<?php echo format_datetime({$expression}); ?>";
        });

        // @currency - Display currency symbol
        Blade::directive('currency', function () {
            return "<?php echo default_currency(); ?>";
        });
    }
}
