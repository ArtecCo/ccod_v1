<?php

namespace App\\Support;

class WebhookMessageFormatter
{
    /**
     * Build a readable, event-aware message without database templates.
     */
    public function format(string $eventKey, array $context): array
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $event = NotificationEventCatalog::event($eventKey) ?? [];
        $label = (string) ($event['label'] ?? $context['event_label'] ?? $eventKey);
        $title = $eventKey === 'sync.completed' ? 'CCOD Azure sync status' : 'CCOD | '.$label;

        if ($eventKey === 'sync.completed') {
            $status = ucfirst(strtolower((string) ($context['overall_status'] ?? 'Unknown')));
            return [
                'title' => 'CCOD Azure sync status',
                'message' => 'Overall status: '.$status,
            ];
        }

        $lines = [];
        foreach (($event['fields'] ?? []) as $key => $definition) {
            if (! array_key_exists($key, $context) || $context[$key] === null || $context[$key] === '') {
                continue;
            }

            $value = $context[$key];
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            $fieldLabel = (string) ($definition['label'] ?? str($key)->replace('_', ' ')->title());
            $lines[] = '**'.$fieldLabel.':** '.(string) $value;
        }

        if ($lines === []) {
            foreach ($context as $key => $value) {
                if (in_array($key, ['event_key', 'event_label', 'is_test', 'test_timestamp'], true) || $value === null || $value === '') {
                    continue;
                }
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
                $lines[] = '**'.str((string) $key)->replace('_', ' ')->title().':** '.(string) $value;
            }
        }

        $message = $lines === []
            ? 'The event was triggered, but no additional event details were supplied.'
            : implode("\\n", $lines);

        if (($context['is_test'] ?? false) === true) {
            $message .= "\\n\\n_Test delivery — sample event data._";
        }

        return ['title' => $title, 'message' => $message];
    }

    /**
     * Recreates the original Azure sync completion Adaptive Card layout.
     */
    public function syncCompletionCard(string $title, array $context): array
    {
        $statusDefinitions = [
            'subscriptions_status' => 'Subscriptions',
            'resources_status' => 'Resources',
            'costs_status' => 'Costs',
            'security_status' => 'Security',
            'alerts_status' => 'Alerts',
            'web_urls_status' => 'Web URLs',
        ];

        $overallStatus = strtoupper((string) ($context['overall_status'] ?? 'FAILED'));
        $overallSuccess = $overallStatus === 'SUCCESS';
        $overallLabel = $overallSuccess ? 'Success' : 'Failed';

        $moduleTile = static function (string $label, string $value): array {
            $success = strtoupper($value) === 'SUCCESS';
            return [
                'type' => 'Container',
                'style' => $success ? 'good' : 'attention',
                'items' => [[
                    'type' => 'TextBlock',
                    'text' => $label.': '.($success ? 'Success' : 'Failed'),
                    'color' => $success ? 'Good' : 'Attention',
                    'weight' => 'Bolder',
                    'wrap' => true,
                ]],
            ];
        };

        $body = [[
            'type' => 'TextBlock',
            'text' => $title,
            'weight' => 'Bolder',
            'size' => 'Large',
            'wrap' => true,
        ], [
            'type' => 'Container',
            'style' => $overallSuccess ? 'good' : 'attention',
            'spacing' => 'Medium',
            'items' => [[
                'type' => 'TextBlock',
                'text' => 'Overall: '.$overallLabel,
                'color' => $overallSuccess ? 'Good' : 'Attention',
                'weight' => 'Bolder',
                'wrap' => true,
            ]],
        ], [
            'type' => 'FactSet',
            'spacing' => 'Medium',
            'facts' => [
                ['title' => 'Started', 'value' => (string) ($context['sync_started_at'] ?? 'Not available')],
                ['title' => 'Completed', 'value' => (string) ($context['completed_at'] ?? 'Not available')],
                ['title' => 'Duration', 'value' => (string) ($context['duration'] ?? 'Not available')],
            ],
        ], [
            'type' => 'TextBlock',
            'text' => 'Module status',
            'weight' => 'Bolder',
            'spacing' => 'Medium',
            'wrap' => true,
        ]];

        $tiles = [];
        foreach ($statusDefinitions as $key => $label) {
            $tiles[] = $moduleTile($label, (string) ($context[$key] ?? 'FAILED'));
        }

        foreach (array_chunk($tiles, 2) as $pair) {
            $columns = [];
            foreach ($pair as $tile) {
                $columns[] = ['type' => 'Column', 'width' => 'stretch', 'items' => [$tile]];
            }
            $body[] = ['type' => 'ColumnSet', 'spacing' => 'Small', 'columns' => $columns];
        }

        $body[] = [
            'type' => 'TextBlock',
            'text' => 'Run ID: '.(string) ($context['run_id'] ?? 'Not available'),
            'size' => 'Small',
            'isSubtle' => true,
            'wrap' => true,
            'spacing' => 'Medium',
        ];

        $card = [
            '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
            'type' => 'AdaptiveCard',
            'version' => '1.2',
            'body' => $body,
            'actions' => [],
        ];

        if (filter_var($context['report_url'] ?? null, FILTER_VALIDATE_URL)) {
            $card['actions'][] = [
                'type' => 'Action.OpenUrl',
                'title' => 'View Sync Report',
                'url' => $context['report_url'],
            ];
        }

        return [
            'type' => 'message',
            'attachments' => [[
                'contentType' => 'application/vnd.microsoft.card.adaptive',
                'contentUrl' => null,
                'content' => $card,
            ]],
        ];
    }
}
