<?php

namespace App\Services;

use App\Models\LogSetting;

class LogSettings
{
    public const LOGIN = 'login';
    public const LOGOUT = 'logout';
    public const FAILED_AUTHENTICATION = 'failed_authentication';
    public const PAGE_REQUESTS = 'page_requests';
    public const APPLICATION_REQUESTS = 'application_requests';
    public const CREATED = 'created';
    public const UPDATED = 'updated';
    public const DELETED = 'deleted';
    public const RESTORED = 'restored';
    public const FORCE_DELETED = 'force_deleted';

    public const GROUPS = [
        'Authentication' => [
            self::LOGIN => 'Login',
            self::LOGOUT => 'Logout',
            self::FAILED_AUTHENTICATION => 'Failed authentication',
        ],
        'Requests' => [
            self::PAGE_REQUESTS => 'Page requests (GET)',
            self::APPLICATION_REQUESTS => 'Application requests (non-GET)',
        ],
        'Model Actions' => [
            self::CREATED => 'Create',
            self::UPDATED => 'Update',
            self::DELETED => 'Delete',
            self::RESTORED => 'Restore',
            self::FORCE_DELETED => 'Force delete',
        ],
    ];

    public const KEYS = [
        self::LOGIN,
        self::LOGOUT,
        self::FAILED_AUTHENTICATION,
        self::PAGE_REQUESTS,
        self::APPLICATION_REQUESTS,
        self::CREATED,
        self::UPDATED,
        self::DELETED,
        self::RESTORED,
        self::FORCE_DELETED,
    ];

    public function enabled(string $key): bool
    {
        return (bool) (LogSetting::query()->where('key', $key)->value('enabled') ?? true);
    }

    public function all(): array
    {
        $stored = LogSetting::query()->pluck('enabled', 'key')->all();

        return collect(self::KEYS)
            ->mapWithKeys(fn (string $key): array => [$key => (bool) ($stored[$key] ?? true)])
            ->all();
    }

    public function save(array $settings): void
    {
        foreach (self::KEYS as $key) {
            LogSetting::query()->updateOrCreate(
                ['key' => $key],
                ['enabled' => (bool) ($settings[$key] ?? false)],
            );
        }
    }
}
