<?php

return [
    'enabled' => true,
    'delete_records_older_than_days' => 365,
    'default_auth_driver' => null,
    'default_log_name' => 'default',
    'database_connection' => env('DB_CONNECTION', 'mysql'),
    'table_name' => 'activity_log',
    'activity_model' => Spatie\Activitylog\Models\Activity::class,
    'subject_returns_soft_deleted_models' => false,
    'causer_returns_soft_deleted_models' => false,
    'activity_logger' => [
        'enabled' => true,
    ],
];
