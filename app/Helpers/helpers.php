<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('settings')) {
    function settings($group)
    {
        return Setting::getByGroup($group);
    }
}

if (!function_exists('getGroupLabel')) {
    function getGroupLabel($group)
    {
        return match ($group) {
            'general' => 'Thông Tin Chung',
            'contact' => 'Thông Tin Liên Hệ',
            'social' => 'Mạng Xã Hội',
            'payment' => 'Phương Thức Thanh Toán',
            'shipping' => 'Vận Chuyển',
            'email' => 'Email Thông Báo',
            'system' => 'Hệ Thống',
            default => ucfirst($group)
        };
    }
}

if (!function_exists('getGroupIcon')) {
    function getGroupIcon($group)
    {
        return match ($group) {
            'general' => 'cog',
            'contact' => 'address-book',
            'social' => 'share-alt',
            'payment' => 'credit-card',
            'shipping' => 'truck',
            'email' => 'envelope',
            'system' => 'server',
            default => 'cog'
        };
    }
}
