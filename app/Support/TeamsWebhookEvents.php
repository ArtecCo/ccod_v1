<?php

namespace App\Support;

use App\Enums\NotificationEvent;

class TeamsWebhookEvents
{
    /**
     * Events currently emitted by CCOD and available for webhook subscriptions.
     *
     * Adding an event here makes it configurable in the portal. The code that
     * produces the event only needs to dispatch its key and context once.
     */
    public static function options(): array
    {
        return [
            'server.started' => 'Server started',
            'azure.sync.completed' => 'Azure synchronization completed',
        ] + NotificationEvent::options();
    }

    public static function defaultsForCategory(?string $category): array
    {
        return match ($category) {
            'server_start' => ['server.started'],
            'sync_status' => ['azure.sync.completed'],
            default => [],
        };
    }

    public static function sampleContext(string $eventKey): array
    {
        $now = now();
        $timestamp = $now->format('Y-m-d H:i:s T');
        $started = $now->copy()->subMinutes(5)->format('Y-m-d H:i:s T');

        if ($eventKey === 'server.started') {
            return [
                'event_key' => $eventKey,
                'event_label' => self::options()[$eventKey],
                'server_boot_time' => $started,
                'iis_status' => 'RUNNING (TEST)',
                'iis_started_at' => $started,
                'php_status' => 'RUNNING (TEST)',
                'php_started_at' => $started,
                'mysql_status' => 'RUNNING (TEST)',
                'mysql_started_at' => $started,
                'queue_status' => 'RUNNING (TEST)',
                'queue_started_at' => $started,
                'is_test' => true,
            ];
        }

        if ($eventKey === 'azure.sync.completed') {
            return [
                'event_key' => $eventKey,
                'event_label' => self::options()[$eventKey],
                'overall_status' => 'SUCCESS (TEST)',
                'sync_started_at' => $started,
                'duration' => '2 minutes (TEST)',
                'subscriptions_status' => 'SUCCESS',
                'resources_status' => 'SUCCESS',
                'costs_status' => 'SUCCESS',
                'security_status' => 'SUCCESS',
                'alerts_status' => 'SUCCESS',
                'web_urls_status' => 'SUCCESS',
                'completed_at' => $timestamp,
                'is_test' => true,
            ];
        }

        return [
            'event_key' => $eventKey,
            'event_label' => self::options()[$eventKey] ?? $eventKey,
            'is_test' => true,
            'test_timestamp' => $timestamp,
        ];
    }
}
