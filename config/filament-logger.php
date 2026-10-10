<?php

return [
    'authorization' => [
        'strict' => true,
        'sensitive_ability' => 'viewSensitiveData',
    ],

    // CCOD stores action metadata only. Model/resource diff logging is disabled
    // so old/new attribute values are never written by Filament Logger.
    'resources' => [
        'enabled' => false,
    ],
    'models' => [
        'enabled' => false,
        'register' => [],
    ],

    'access' => [
        'enabled' => true,
        'guards' => ['web', 'developers'],
        'store_ip' => true,
        'anonymize_ip' => false,
        'store_user_agent' => true,
        'events' => [
            'login' => true,
            'logout' => true,
            'failed' => true,
            'lockout' => true,
            'password_reset' => true,
            'two_factor_recovery' => true,
        ],
    ],

    'notifications' => [
        'enabled' => false,
    ],

    'dashboard' => [
        'enabled' => true,
        'lookback_days' => 30,
        'top_limit' => 10,
    ],

    'exports' => [
        'enabled' => true,
        'ability' => 'exportActivity',
        'manage_ability' => 'manageExportPresets',
        'columns' => [
            'id',
            'log_name',
            'event',
            'description',
            'subject_type',
            'subject_id',
            'causer_type',
            'causer_id',
            'causer_name',
            'risk',
            'tags',
            'properties',
            'created_at',
        ],
    ],

    'sensitive_keys' => [
        'password',
        'password_confirmation',
        'current_password',
        'secret',
        'client_secret',
        'api_key',
        'private_key',
        'token',
        'api_token',
        'access_token',
        'refresh_token',
        'remember_token',
        'authorization',
        'cookie',
    ],

    'pruning' => [
        'days' => 365,
    ],

    'scoped_to_tenant' => false,
];
