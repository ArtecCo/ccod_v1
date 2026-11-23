<?php

namespace App\Support;

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
            : implode("\n", $lines);

        if (($context['is_test'] ?? false) === true) {
            $message .= "\n\n_Test delivery — sample event data._";
        }

        return ['title' => $title, 'message' => $message];
    }

    /**
     * Build a status-focused Adaptive Card for server startup.
     */
    public function serverStartedCard(string $title, array $context): array
    {
        $components = [
            ['label' => 'IIS', 'status' => 'iis_status', 'started' => 'iis_started_at'],
            ['label' => 'PHP', 'status' => 'php_status', 'started' => 'php_started_at'],
            ['label' => 'MySQL', 'status' => 'mysql_status', 'started' => 'mysql_started_at'],
            ['label' => 'Queue Worker', 'status' => 'queue_status', 'started' => 'queue_started_at'],
        ];

        $body = [[
            'type' => 'TextBlock',
            'text' => $title,
            'weight' => 'Bolder',
            'size' => 'Medium',
            'wrap' => true,
        ]];

        foreach ($components as $component) {
            $status = strtoupper((string) ($context[$component['status']] ?? 'UNKNOWN'));
            $healthy = in_array($status, ['RUNNING', 'CONFIGURED'], true);
            $statusLabel = $status === 'CONFIGURED' ? 'CONFIGURED' : ($healthy ? 'RUNNING' : $status);

            $body[] = [
                'type' => 'Container',
                'spacing' => 'Small',
                'items' => [[
                    'type' => 'ColumnSet',
                    'columns' => [[
                        'type' => 'Column',
                        'width' => 'stretch',
                        'items' => [[
                            'type' => 'TextBlock',
                            'text' => $component['label'],
                            'weight' => 'Bolder',
                            'spacing' => 'None',
                            'wrap' => true,
                        ]],
                    ], [
                        'type' => 'Column',
                        'width' => 'auto',
                        'items' => [[
                            'type' => 'TextBlock',
                            'text' => $statusLabel,
                            'weight' => 'Bolder',
                            'color' => $healthy ? 'Good' : 'Attention',
                            'spacing' => 'None',
                            'horizontalAlignment' => 'Right',
                            'wrap' => true,
                        ]],
                    ]],
                ], [
                    'type' => 'TextBlock',
                    'text' => $component['label'] === 'PHP' && $status === 'CONFIGURED'
                        ? 'IIS FastCGI · on demand  |  Worker: '.(string) ($context['iis_worker_started_at'] ?? 'Not available')
                        : 'Started: '.(string) ($context[$component['started']] ?? 'Not available'),
                    'size' => 'Small',
                    'isSubtle' => true,
                    'spacing' => 'None',
                    'wrap' => true,
                ]],
            ];
        }

        return [
            'type' => 'message',
            'attachments' => [[
                'contentType' => 'application/vnd.microsoft.card.adaptive',
                'contentUrl' => null,
                'content' => [
                    '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                    'type' => 'AdaptiveCard',
                    'version' => '1.2',
                    'body' => $body,
                ],
            ]],
        ];
    }

    /**
     * Build a score-focused Adaptive Card for Azure security score changes.
     */
    public function securityScoreCard(string $title, array $context): array
    {
        $currentScore = (float) ($context['current_score'] ?? 0);
        $previousScore = (float) ($context['previous_score'] ?? 0);
        $scoreDelta = (float) ($context['score_delta'] ?? ($currentScore - $previousScore));
        $critical = $currentScore < 0.8;
        $severity = $critical ? 'Critical' : 'Warning';

        $formatPercent = static fn (float $score): string => number_format($score * 100, 2).' %';
        $formatDelta = static fn (float $delta): string => ($delta > 0 ? '+' : '').number_format($delta * 100, 2).' %';

        return [
            'type' => 'message',
            'attachments' => [[
                'contentType' => 'application/vnd.microsoft.card.adaptive',
                'contentUrl' => null,
                'content' => [
                    '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json',
                    'type' => 'AdaptiveCard',
                    'version' => '1.2',
                    'body' => [[
                        'type' => 'ColumnSet',
                        'columns' => [[
                            'type' => 'Column',
                            'width' => 'stretch',
                            'items' => [[
                                'type' => 'TextBlock',
                                'text' => $title,
                                'weight' => 'Bolder',
                                'size' => 'Medium',
                                'wrap' => true,
                            ]],
                        ], [
                            'type' => 'Column',
                            'width' => 'auto',
                            'verticalContentAlignment' => 'Center',
                            'items' => [[
                                'type' => 'Container',
                                'style' => $critical ? 'attention' : 'warning',
                                'items' => [[
                                    'type' => 'TextBlock',
                                    'text' => $severity,
                                    'weight' => 'Bolder',
                                    'size' => 'Small',
                                    'wrap' => false,
                                ]],
                            ]],
                        ]],
                    ], [
                        'type' => 'TextBlock',
                        'text' => (string) ($context['subscription_name'] ?? $context['subscription_id'] ?? 'Azure subscription'),
                        'wrap' => true,
                        'spacing' => 'Small',
                    ], [
                        'type' => 'FactSet',
                        'spacing' => 'Small',
                        'facts' => [
                            ['title' => 'Previous score', 'value' => $formatPercent($previousScore)],
                            ['title' => 'Latest score', 'value' => $formatPercent($currentScore)],
                            ['title' => 'Change', 'value' => $formatDelta($scoreDelta)],
                        ],
                    ]],
                ],
            ]],
        ];
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
