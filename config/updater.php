<?php

declare(strict_types=1);

return [
    'repository' => env('UPDATER_REPOSITORY'),
    'token' => env('UPDATER_GITHUB_TOKEN'),
    'api_url' => env('UPDATER_API_URL', 'https://api.github.com'),
    'strategy' => env('UPDATER_STRATEGY', 'git'),
    'current_version' => env('UPDATER_CURRENT_VERSION', '0.0.0'),
    'ability' => 'updater.manage',
    'check_cache_minutes' => 10,
    'schedule' => env('UPDATER_SCHEDULE', '0 */6 * * *'),
    'timeout' => 900,
    'maintenance' => [
        'enabled' => true,
        'retry' => 60,
        'secret' => env('UPDATER_MAINTENANCE_SECRET'),
    ],
    'binaries' => [
        'php' => env('UPDATER_PHP_BINARY'),
        'composer' => env('UPDATER_COMPOSER_BINARY', 'composer'),
        'npm' => env('UPDATER_NPM_BINARY', 'npm'),
        'git' => env('UPDATER_GIT_BINARY', 'git'),
    ],
    'environment' => [
        'HOME' => env('UPDATER_HOME'),
        'COMPOSER_HOME' => env('UPDATER_COMPOSER_HOME'),
    ],
    'steps' => [
        ['@composer', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'],
        ['@php', 'artisan', 'migrate', '--force'],
        ['@npm', 'ci', '--no-audit', '--no-fund'],
        ['@npm', 'run', 'build'],
        ['@php', 'artisan', 'optimize:clear'],
        ['@php', 'artisan', 'optimize'],
        ['@php', 'artisan', 'queue:restart'],
    ],
    'recovery_steps' => [
        ['@composer', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'],
        ['@php', 'artisan', 'optimize:clear'],
    ],
    'package_steps' => [
        ['@composer', 'update', '{packages}', '--with-dependencies', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'],
        ['@php', 'artisan', 'migrate', '--force'],
        ['@php', 'artisan', 'optimize:clear'],
        ['@php', 'artisan', 'optimize'],
        ['@php', 'artisan', 'queue:restart'],
    ],
    'package_recovery_steps' => [
        ['@composer', 'install', '--no-dev', '--no-interaction', '--prefer-dist', '--optimize-autoloader'],
        ['@php', 'artisan', 'optimize:clear'],
    ],
    'git_ignored_changes' => ['composer.lock'],
    'preserve' => ['.env', '.git', 'storage', 'vendor', 'node_modules', 'bootstrap/cache', 'public/storage'],
    'runner' => env('UPDATER_RUNNER', PHP_OS_FAMILY === 'Windows' ? 'queue' : 'process'),
    'route_prefix' => 'updater',
    'queue' => [
        'connection' => env('UPDATER_QUEUE_CONNECTION'),
        'name' => env('UPDATER_QUEUE'),
        'timeout' => 3600,
    ],
    'cache_store' => env('UPDATER_CACHE_STORE'),
];
