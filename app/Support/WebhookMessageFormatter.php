<?php

namespace App\Support;

class WebhookMessageFormatter
{
    /**
     * Build a consistent, event-aware webhook message without database templates.
     * The event catalogue owns field names and labels; this class owns presentation.
     */
    public function format(string $eventKey, array $context): array
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $event = NotificationEventCatalog::event($eventKey) ?? [];
        $label = (string) ($event['label'] ?? $context['event_label'] ?? $eventKey);
        $title = 'CCOD | '.$label;

        $fields = $event['fields'] ?? [];
        $lines = [];

        foreach ($fields as $key => $definition) {
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

                $fieldLabel = str((string) $key)->replace('_', ' ')->title();
                $lines[] = '**'.$fieldLabel.':** '.(string) $value;
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
}
