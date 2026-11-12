<?php

namespace App\Support;

use App\Enums\NotificationEvent;

class NotificationEventCatalog
{
    private const COMMON_FIELDS = [
        'event_key' => ['label' => 'Event key', 'type' => 'string', 'description' => 'Stable identifier for the event.'],
        'event_label' => ['label' => 'Event name', 'type' => 'string', 'description' => 'Human-readable event name.'],
        'is_test' => ['label' => 'Test notification', 'type' => 'boolean', 'description' => 'Whether this is a test delivery.'],
    ];

    private const EVENTS = [
        'server.started' => [
            'label' => 'Server started',
            'category' => 'Server',
            'description' => 'Raised by the Windows server startup task after it inspects IIS, PHP FastCGI, MySQL and the Laravel queue.',
            'scope' => ['global'],
            'fields' => [
                'server_boot_time' => ['label' => 'Server boot time', 'type' => 'datetime'],
                'iis_status' => ['label' => 'IIS status', 'type' => 'enum', 'values' => ['RUNNING' => 'Running', 'NOT RUNNING' => 'Not running']],
                'iis_started_at' => ['label' => 'IIS start time', 'type' => 'datetime'],
                'php_status' => ['label' => 'PHP FastCGI status', 'type' => 'enum', 'values' => ['RUNNING' => 'Running', 'NOT DETECTED' => 'Not detected']],
                'php_started_at' => ['label' => 'PHP FastCGI start time', 'type' => 'datetime'],
                'mysql_status' => ['label' => 'MySQL status', 'type' => 'enum', 'values' => ['RUNNING' => 'Running', 'STOPPED' => 'Stopped', 'NOT DETECTED' => 'Not detected']],
                'mysql_started_at' => ['label' => 'MySQL start time', 'type' => 'datetime'],
                'queue_status' => ['label' => 'Laravel queue status', 'type' => 'enum', 'values' => ['RUNNING' => 'Running', 'NOT RUNNING' => 'Not running']],
                'queue_started_at' => ['label' => 'Laravel queue start time', 'type' => 'datetime'],
            ],
            'sample' => [
                'server_boot_time' => '2026-11-12 08:00:00',
                'iis_status' => 'RUNNING',
                'iis_started_at' => '2026-11-12 08:01:00',
                'php_status' => 'RUNNING',
                'php_started_at' => '2026-11-12 08:02:00',
                'mysql_status' => 'RUNNING',
                'mysql_started_at' => '2026-11-12 08:00:30',
                'queue_status' => 'RUNNING',
                'queue_started_at' => '2026-11-12 08:03:00',
            ],
        ],
        'sync.started' => [
            'label' => 'Azure synchronization started',
            'category' => 'Azure',
            'description' => 'Raised when the unified Azure scheduler begins a synchronization run.',
            'scope' => ['global'],
            'fields' => [
                'run_id' => ['label' => 'Sync run ID', 'type' => 'string'],
                'sync_started_at' => ['label' => 'Sync start time', 'type' => 'datetime'],
            ],
            'sample' => [
                'run_id' => '20261112-090000',
                'sync_started_at' => '2026-11-12 09:00:00',
            ],
        ],
        'sync.completed' => [
            'label' => 'Azure synchronization completed',
            'category' => 'Azure',
            'description' => 'Raised once the unified scheduler has finished all six synchronization modules.',
            'scope' => ['global', 'subscription'],
            'fields' => [
                'overall_status' => ['label' => 'Overall status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'sync_started_at' => ['label' => 'Sync start time', 'type' => 'datetime'],
                'completed_at' => ['label' => 'Sync completion time', 'type' => 'datetime'],
                'duration' => ['label' => 'Formatted duration', 'type' => 'string'],
                'duration_seconds' => ['label' => 'Duration (seconds)', 'type' => 'number'],
                'subscriptions_status' => ['label' => 'Subscriptions module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'resources_status' => ['label' => 'Resources module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'costs_status' => ['label' => 'Costs module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'security_status' => ['label' => 'Security module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'alerts_status' => ['label' => 'Alerts module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'web_urls_status' => ['label' => 'Web URLs module status', 'type' => 'enum', 'values' => ['SUCCESS' => 'Success', 'FAILED' => 'Failed']],
                'failed_modules' => ['label' => 'Failed modules', 'type' => 'array'],
                'successful_modules' => ['label' => 'Successful modules', 'type' => 'array'],
                'error_summary' => ['label' => 'Error summary', 'type' => 'string'],
            ],
            'sample' => [
                'overall_status' => 'SUCCESS',
                'sync_started_at' => '2026-11-12 09:00:00',
                'completed_at' => '2026-11-12 09:02:00',
                'duration' => '2m 0.000s',
                'duration_seconds' => 120,
                'subscriptions_status' => 'SUCCESS',
                'resources_status' => 'SUCCESS',
                'costs_status' => 'SUCCESS',
                'security_status' => 'SUCCESS',
                'alerts_status' => 'SUCCESS',
                'web_urls_status' => 'SUCCESS',
                'failed_modules' => [],
                'successful_modules' => ['Subscriptions', 'Resources', 'Costs', 'Security', 'Alerts', 'Web URLs'],
                'error_summary' => '',
            ],
        ],
    ];

    public static function all(): array
    {
        $events = self::EVENTS;

        foreach (NotificationEvent::cases() as $event) {
            $events[$event->value] = [
                'label' => $event->label(),
                'category' => self::categoryForLegacyEvent($event->value),
                'description' => 'Application event. Additional fields become available when its producer registers them in this catalogue.',
                'scope' => ['global'],
                'fields' => [],
                'sample' => [],
            ];
        }

        return $events;
    }

    public static function options(): array
    {
        return collect(self::all())
            ->mapWithKeys(fn (array $event, string $key): array => [$key => $event['label']])
            ->all();
    }

    public static function event(string $key): ?array
    {
        $key = self::normalizeKey($key);

        return self::all()[$key] ?? null;
    }

    public static function normalizeKey(string $key): string
    {
        return $key === 'azure.sync.completed' ? 'sync.completed' : $key;
    }

    public static function normalizeKeys(array|string|null $keys): array
    {
        $keys = is_array($keys) ? $keys : (is_string($keys) && $keys !== '' ? [$keys] : []);

        return array_values(array_unique(array_map(
            static fn ($key): string => self::normalizeKey((string) $key),
            $keys,
        )));
    }

    public static function fieldsForEvents(array|string|null $eventKeys): array
    {
        $fields = self::COMMON_FIELDS;

        foreach (self::normalizeKeys($eventKeys) as $eventKey) {
            foreach (self::event($eventKey)['fields'] ?? [] as $key => $definition) {
                $fields[$key] = $definition;
            }
        }

        return $fields;
    }

    public static function fieldOptions(array|string|null $eventKeys): array
    {
        return collect(self::fieldsForEvents($eventKeys))
            ->mapWithKeys(fn (array $field, string $key): array => [$key => $field['label'] ?? $key])
            ->all();
    }

    public static function field(array|string|null $eventKeys, ?string $fieldKey): ?array
    {
        if (! $fieldKey) {
            return null;
        }

        return self::fieldsForEvents($eventKeys)[$fieldKey] ?? null;
    }

    public static function operatorsForField(array|string|null $eventKeys, ?string $fieldKey): array
    {
        $field = self::field($eventKeys, $fieldKey);
        $type = $field['type'] ?? 'string';

        $operators = [
            'equals' => 'Equals',
            'not_equals' => 'Does not equal',
            'exists' => 'Exists',
            'not_exists' => 'Does not exist',
        ];

        if (in_array($type, ['string', 'enum', 'array'], true)) {
            $operators += ['contains' => 'Contains', 'not_contains' => 'Does not contain', 'in' => 'Is one of'];
        }

        if (in_array($type, ['number'], true)) {
            $operators += [
                'greater_than' => 'Greater than',
                'greater_or_equal' => 'Greater than or equal',
                'less_than' => 'Less than',
                'less_or_equal' => 'Less than or equal',
            ];
        }

        return $operators;
    }

    public static function valuesForField(array|string|null $eventKeys, ?string $fieldKey): array
    {
        $field = self::field($eventKeys, $fieldKey);

        return ($field['type'] ?? null) === 'enum' ? ($field['values'] ?? []) : [];
    }

    public static function placeholderOptions(array|string|null $eventKeys): array
    {
        return collect(self::fieldsForEvents($eventKeys))
            ->mapWithKeys(fn (array $field, string $key): array => ['{' . $key . '}' => ($field['label'] ?? $key) . ' — {' . $key . '}'])
            ->all();
    }

    public static function sampleContext(string $eventKey): array
    {
        $eventKey = self::normalizeKey($eventKey);
        $event = self::event($eventKey);
        $context = $event['sample'] ?? [];
        $context['event_key'] = $eventKey;
        $context['event_label'] = $event['label'] ?? $eventKey;
        $context['is_test'] = true;

        return $context;
    }

    private static function categoryForLegacyEvent(string $key): string
    {
        return match (true) {
            str_starts_with($key, 'azure_') => 'Azure',
            str_starts_with($key, 'servicenow_') => 'ServiceNow',
            str_starts_with($key, 'user_'), str_starts_with($key, 'subscription_') => 'Access and teams',
            str_starts_with($key, 'maintenance_') => 'Maintenance',
            default => 'Application',
        };
    }
}
