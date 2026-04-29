<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("app_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            // Cast boolean values
            if ($setting->type === 'boolean') {
                return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
            }

            // Cast numeric values
            if ($setting->type === 'number') {
                return is_numeric($setting->value) ? (float) $setting->value : $default;
            }

            return $setting->value ?? $default;
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): bool
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        // Convert boolean to string
        if ($setting->type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        $setting->value = $value;
        $setting->save();

        // Clear cache
        Cache::forget("app_setting_{$key}");

        return true;
    }

    /**
     * Get all settings grouped by category
     */
    public static function getAllGrouped(): array
    {
        return self::query()->orderBy('group')->orderBy('label')->get()->groupBy('group')->all();
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group)
    {
        return self::query()->where('group', $group)->orderBy('label')->get();
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache(): void
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget("app_setting_{$setting->key}");
        }
    }

    /**
     * Boot method to clear cache on update
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($setting) {
            Cache::forget("app_setting_{$setting->key}");
        });

        static::deleted(function ($setting) {
            Cache::forget("app_setting_{$setting->key}");
        });
    }
}
