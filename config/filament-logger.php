<?php

use MrAdder\FilamentLogger\Resources\ActivityResource;

return [
    'activity_resource' => ActivityResource::class,

    'authorization' => [
        'strict' => true,
        'sensitive_ability' => 'viewSensitiveData',
    ],

    // CCOD uses its own developer-only audit resource. Package resource/model
    // lifecycle logging is disabled so audit records are created centrally by
    // App\Services\AuditLogger without recording old/new model values.
    'resources' => [
        'enabled' => false,
    ],
    'models' => [
        'enabled' => false,
        'register' => [],
    ],

    // Authentication events are handled by AppServiceProvider so that both
    // application guards are logged consistently without package listeners.
    'access' => [
        'enabled' => false,
        'guards' => ['web', 'developers'],
        'store_ip' => true,
        'anonymize_ip' => false,
        'store_user_agent' => true,
        'events' => [
            'login' => false,
            'logout' => false,
            'failed' => false,
            'lockout' => false,
            'password_reset' => false,
            'two_factor_recovery' => false,
        ],
    ],

    'notifications' => [
        'enabled' => false,
    ],

    'dashboard' => [
        'enabled' => false,
        'lookback_days' => 30,
        'top_limit' => 10,
    ],

    'exports' => [
        'enabled' => false,
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
