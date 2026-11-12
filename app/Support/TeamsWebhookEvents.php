<?php

namespace App\Support;

class TeamsWebhookEvents
{
    public static function options(): array
    {
        return NotificationEventCatalog::options();
    }

    public static function defaultsForCategory(?string $category): array
    {
        return match ($category) {
            'server_start' => ['server.started'],
            'sync_status' => ['sync.completed'],
            default => [],
        };
    }

    public static function sampleContext(string $eventKey): array
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $event = NotificationEventCatalog::event($eventKey);
        $now = now();
        $timestamp = $now->format('Y-m-d H:i:s T');
        $started = $now->copy()->subMinutes(5)->format('Y-m-d H:i:s T');

        $context = array_merge(
            NotificationEventCatalog::COMMON_FIELDS ?? [],
            $event['sample'] ?? [],
        );

        $context['event_key'] = $eventKey;
        $context['event_label'] = $event['label'] ?? $eventKey;
        $context['is_test'] = true;

        if ($eventKey === 'server.started') {
            $context['server_boot_time'] = $started;
            foreach (['iis_status', 'php_status', 'mysql_status', 'queue_status'] as $field) {
                $context[$field] = ($context[$field] ?? 'RUNNING').' (TEST)';
            }
            foreach (['iis_started_at', 'php_started_at', 'mysql_started_at', 'queue_started_at'] as $field) {
                $context[$field] = $started;
            }
        }

        if ($eventKey === 'sync.completed') {
            $context['sync_started_at'] = $started;
            $context['completed_at'] = $timestamp;
            $context['duration'] = '2 minutes (TEST)';
            $context['duration_seconds'] = 120;
        }

        $context['test_timestamp'] = $timestamp;

        return $context;
    }
}
