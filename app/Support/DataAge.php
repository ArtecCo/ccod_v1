<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\HtmlString;

class DataAge
{
    public static function for(?CarbonInterface $syncedAt): array
    {
        if ($syncedAt === null) {
            return [
                new HtmlString('<span style="display:inline-flex;align-items:center;padding:3px 9px;border-radius:9999px;background:#fee2e2;color:#b91c1c;font-size:12px;font-weight:600;">Sync time unavailable — data freshness unknown</span>'),
                'danger',
            ];
        }

        $seconds = (int) max(0, $syncedAt->diffInSeconds(now()));
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            $age = $days . ' ' . ($days === 1 ? 'day' : 'days');
            if ($hours > 0) {
                $age .= ' ' . $hours . ' hr';
            }
        } elseif ($hours > 0) {
            $age = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
            if ($minutes > 0) {
                $age .= ' ' . $minutes . ' min';
            }
        } else {
            $age = $minutes > 0 ? $minutes . ' min' : 'less than a minute';
        }

        $stale = $seconds > 8 * 60 * 60;

        return [
            $stale
                ? new HtmlString('<span style="display:inline-flex;align-items:center;padding:3px 9px;border-radius:9999px;background:#fee2e2;color:#b91c1c;font-size:12px;font-weight:600;">Stale data · ' . e('Data is ' . $age . ' old') . '</span>')
                : 'Data age: ' . $age,
            $stale ? 'danger' : 'gray',
        ];
    }
}
