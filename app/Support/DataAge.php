<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;

class DataAge
{
    public static function for(?CarbonInterface $syncedAt): array
    {
        if ($syncedAt === null) {
            return ['Data age unavailable', 'danger'];
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
            $stale ? 'Stale data · ' . $age . ' old' : 'Data age: ' . $age,
            $stale ? 'danger' : 'gray',
        ];
    }
}
