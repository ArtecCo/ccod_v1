<?php

namespace App\Services;

use App\Models\LogSetting;

class LogSettings
{
    public const REQUESTS = 'requests';
    public const AUTHENTICATION = 'authentication';
    public const MODEL_ACTIONS = 'model_actions';

    public const KEYS = [
        self::REQUESTS,
        self::AUTHENTICATION,
        self::MODEL_ACTIONS,
    ];

    public function enabled(string $key): bool
    {
        return LogSetting::query()->where('key', $key)->value('enabled') ?? true;
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
