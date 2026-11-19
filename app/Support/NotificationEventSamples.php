<?php

namespace App\Support;

/**
 * Stable example payloads used by webhook-template test delivery.
 * These samples are independent of event routing and rule configuration.
 */
class NotificationEventSamples
{
    public static function sampleContext(string $eventKey): array
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $context = NotificationEventCatalog::sampleContext($eventKey);
        $now = now();
        $timestamp = $now->format('Y-m-d H:i:s T');
        $started = $now->copy()->subMinutes(5)->format('Y-m-d H:i:s T');

        if ($eventKey === 'server.started') {
            $context['server_boot_time'] = $started;
            foreach (['iis_status', 'php_status', 'mysql_status', 'queue_status'] as $field) {
                $context[$field] = ($context[$field] ?? 'RUNNING') . ' (TEST)';
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
            $context['overall_status'] = 'FAILED';
            $context['subscriptions_status'] = 'SUCCESS';
            $context['resources_status'] = 'FAILED';
            $context['costs_status'] = 'SUCCESS';
            $context['security_status'] = 'SUCCESS';
            $context['alerts_status'] = 'FAILED';
            $context['web_urls_status'] = 'SUCCESS';
            $context['run_id'] = 'TEST-' . $now->format('Ymd-His');
            $context['report_url'] = rtrim((string) config('app.url'), '/') . '/sync-report/latest';
        }

        $context['test_timestamp'] = $timestamp;

        return $context;
    }
}
