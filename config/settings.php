<?php

use App\Settings\MaintenanceSettings;
use App\Settings\StylingSettings;
use App\Settings\TabAccessSettings;

return [
    'settings' => [
        StylingSettings::class,
        MaintenanceSettings::class,
        TabAccessSettings::class,
    ],

    'setting_class_path' => app_path('Settings'),

    'migrations_paths' => [
        database_path('settings'),
    ],

    'default_repository' => 'database',

    'repositories' => [
        'database' => [
            'type' => Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository::class,
            'model' => null,
            'table' => null,
            'connection' => null,
        ],
    ],

    'encoder' => null,
    'decoder' => null,

    'cache' => [
        'enabled' => true,
        'store' => null,
        'prefix' => null,
        'ttl' => null,
    ],
];
