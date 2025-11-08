<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description'
    ];

    /**
     * Lấy giá trị setting theo key
     */
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set giá trị setting
     */
    public static function set($key, $value, $type = 'text', $group = 'general', $label = null, $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'label' => $label,
                'description' => $description
            ]
        );

        Cache::forget("setting.{$key}");
        Cache::forever("setting.{$key}", $value);

        return $setting;
    }

    /**
     * Lấy tất cả settings theo group
     */
    public static function getByGroup($group)
    {
        return Cache::rememberForever("settings.group.{$group}", function () use ($group) {
            return self::where('group', $group)->pluck('value', 'key');
        });
    }

    /**
     * Clear cache khi update
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });

        static::deleted(function ($setting) {
            Cache::forget("setting.{$setting->key}");
            Cache::forget("settings.group.{$setting->group}");
        });
    }
}
