<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for delivery tracking and client
    | notification settings.
    |
    */

    // Cache settings for tracking data
    'cache' => [
        'duration' => 30, // days
        'prefix' => 'tracking_order_',
    ],

    // Notification settings
    'notifications' => [
        'email_enabled' => env('TRACKING_EMAIL_NOTIFICATIONS', true),
        'sms_enabled' => env('TRACKING_SMS_NOTIFICATIONS', false),
    ],

    // Real-time sync settings
    'real_time' => [
        'enabled' => env('TRACKING_REAL_TIME_SYNC', true),
        'update_interval' => 300, // seconds
    ],

    // Client tracking settings
    'client_tracking' => [
        'enabled' => true,
        'auto_sync' => true,
        'cache_duration' => 30, // days
    ],
]; 