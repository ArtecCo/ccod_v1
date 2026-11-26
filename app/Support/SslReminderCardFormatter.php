<?php

namespace App\Support;

class SslReminderCardFormatter
{
    public function format(string $title, array $context): array
    {
        $severity = strtolower((string) ($context['severity'] ?? $context['ssl_reminder_severity'] ?? 'reminder'));
        $presentation = match ($severity) {
            'attention' => ['label' => '🟡 WARNING', 'style' => 'warning', 'color' => 'Warning'],
            'warning' => ['label' => '🔴 ATTENTION', 'style' => 'attention', 'color' => 'Attention'],
            'critical' => ['label' => '🔴 CRITICAL', 'style' => 'attention', 'color' => 'Attention'],
            default => ['label' => '🟡 REMINDER', 'style' => 'warning', 'color' => 'Warning'],
        };

        $facts = [];
        foreach ([
            'domain' => 'Domain',
            'url' => 'Website URL',
            'certificate_expires_at' => 'Certificate expires',
            'days_until_expiry' => 'Days remaining',
            'ssl_reminder_threshold_days' => 'Reminder threshold',
        ] as $key => $label) {
            $value = $context[$key] ?? null;
            if ($value === null || $value === '') {
                continue;
            }
            if ($key === 'days_until_expiry' || $key === 'ssl_reminder_threshold_days') {
                $value = (string) $value.' days';
            }
            $facts[] = ['title' => $label, 'value' => (string) $value];
        }

        $body = [[
            'type' => 'Container',
            'style' => $presentation['style'],
            'bleed' => true,
            'spacing' => 'None',
            'items' => [[
                'type' => 'TextBlock',
                'text' => $presentation['label'].' - SSL CERTIFICATE EXPIRY',
                'weight' => 'Bolder',
                'size' => 'Large',
                'color' => $presentation['color'],
                'horizontalAlignment' => 'Center',
                'wrap' => true,
                'spacing' => 'Small',
            ]],
        ], [
            'type' => 'TextBlock',
            'text' => $title,
            'weight' => 'Bolder',
            'size' => 'Large',
            'wrap' => true,
            'spacing' => 'Medium',
        ], [
            'type' => 'FactSet',
            'spacing' => 'Small',
            'facts' => $facts,
        ]];

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
}
