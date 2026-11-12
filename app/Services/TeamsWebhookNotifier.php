<?php

namespace App\Services;

use App\Models\TeamsWebhook;
use App\Support\TeamsWebhookEvents;
use App\Support\NotificationEventCatalog;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeamsWebhookNotifier
{
    public function sendServerStart(array $context): void
    {
        $this->dispatch('server.started', $context);
    }

    public function sendSyncStatus(array $context): void
    {
        $this->dispatch('sync.completed', $context);
    }

    /**
     * Dispatch a named CCOD event to every enabled destination subscribed to it.
     * Producers do not know about categories, templates, URLs or message formats.
     */
    public function dispatch(string $eventKey, array $context = []): int
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $context = array_merge($context, [
            'event_key' => $eventKey,
            'event_label' => TeamsWebhookEvents::options()[$eventKey] ?? $eventKey,
        ]);

        $sent = 0;

        TeamsWebhook::query()
            ->where('enabled', true)
            ->whereJsonContains('event_keys', $eventKey)
            ->with('template')
            ->orderBy('id')
            ->chunkById(100, function ($webhooks) use ($context, &$sent): void {
                foreach ($webhooks as $webhook) {
                    if (! $this->matchesConditions($webhook, $context)) {
                        continue;
                    }

                    if ($this->deliver($webhook, $context)) {
                        $sent++;
                    }
                }
            });

        return $sent;
    }

    public function sendTest(TeamsWebhook $webhook): bool
    {
        $eventKey = NotificationEventCatalog::normalizeKeys($webhook->event_keys)[0] ?? 'server.started';
        $context = TeamsWebhookEvents::sampleContext($eventKey);

        return $this->deliver($webhook, $context);
    }

    private function matchesConditions(TeamsWebhook $webhook, array $context): bool
    {
        $conditions = $webhook->conditions ?? [];

        if ($conditions === []) {
            return true;
        }

        $results = [];
        foreach ($conditions as $condition) {
            $field = trim((string) ($condition['field'] ?? ''));
            if ($field !== '' && ! array_key_exists($field, NotificationEventCatalog::fieldsForEvents([$context['event_key'] ?? '']))) {
                $results[] = false;
                continue;
            }
            $operator = $condition['operator'] ?? 'equals';
            $expected = $condition['value'] ?? $condition['value_text'] ?? null;
            $actual = $field !== '' ? data_get($context, $field) : null;
            $actualString = is_bool($actual) ? ($actual ? 'true' : 'false') : (is_scalar($actual) ? mb_strtolower((string) $actual) : '');
            $expectedString = is_scalar($expected) ? mb_strtolower((string) $expected) : '';

            $results[] = match ($operator) {
                'equals' => $actual !== null && $actualString === $expectedString,
                'not_equals' => $actual !== null && $actualString !== $expectedString,
                'contains' => is_array($actual)
                    ? in_array($expected, $actual, true)
                    : ($actual !== null && str_contains($actualString, $expectedString)),
                'not_contains' => is_array($actual)
                    ? ! in_array($expected, $actual, true)
                    : ($actual !== null && ! str_contains($actualString, $expectedString)),
                'greater_than' => $this->compareValues($actual, $expected, 'greater_than'),
                'greater_or_equal' => $this->compareValues($actual, $expected, 'greater_or_equal'),
                'less_than' => $this->compareValues($actual, $expected, 'less_than'),
                'less_or_equal' => $this->compareValues($actual, $expected, 'less_or_equal'),
                'in' => $actual !== null && (function () use ($actual, $actualString, $expected): bool {
                    $expectedValues = is_array($expected)
                        ? $expected
                        : explode(',', (string) $expected);
                    $expectedValues = array_map(
                        static fn ($value): string => mb_strtolower(trim((string) $value)),
                        $expectedValues,
                    );

                    if (is_array($actual)) {
                        $actualValues = array_map(
                            static fn ($value): string => mb_strtolower(trim((string) $value)),
                            $actual,
                        );

                        return count(array_intersect($actualValues, $expectedValues)) > 0;
                    }

                    return in_array($actualString, $expectedValues, true);
                })(),
                'exists' => $actual !== null,
                'not_exists' => $actual === null,
                default => false,
            };
        }

        return ($webhook->condition_mode ?? 'all') === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    private function compareValues(mixed $actual, mixed $expected, string $operator): bool
    {
        if ($actual === null || $expected === null) {
            return false;
        }

        if (is_numeric($actual) && is_numeric($expected)) {
            $left = (float) $actual;
            $right = (float) $expected;
        } else {
            $left = strtotime((string) $actual);
            $right = strtotime((string) $expected);

            if ($left === false || $right === false) {
                return false;
            }
        }

        return match ($operator) {
            'greater_than' => $left > $right,
            'greater_or_equal' => $left >= $right,
            'less_than' => $left < $right,
            'less_or_equal' => $left <= $right,
            default => false,
        };
    }

    private function deliver(TeamsWebhook $webhook, array $context): bool
    {
        try {
            $url = $webhook->webhook_url;
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException('Webhook URL must use HTTPS and a valid hostname.');
            }

            $template = $webhook->template;
            $titleTemplate = $template?->title_template ?: $webhook->title_template ?: $this->defaultTitle($webhook->category);
            $messageTemplate = $template?->message_template ?: $webhook->message_template;
            $title = $this->render($titleTemplate, $context);
            $message = $this->render($messageTemplate, $context);
            $payload = $this->payload($webhook, $title, $message);

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->withOptions(['allow_redirects' => false])
                ->post($url, $payload);

            if (! $response->successful()) {
                throw new \RuntimeException('Teams returned HTTP '.$response->status().'.');
            }

            $webhook->forceFill([
                'last_delivery_status' => 'success',
                'last_sent_at' => now(),
                'last_error' => null,
            ])->save();

            return true;
        } catch (Throwable $exception) {
            report($exception);

            $webhook->forceFill([
                'last_delivery_status' => 'failed',
                'last_sent_at' => now(),
                'last_error' => mb_substr($exception->getMessage(), 0, 2000),
            ])->save();

            return false;
        }
    }

    private function payload(TeamsWebhook $webhook, string $title, string $message): array
    {
        // Adaptive Cards are supported for both Teams channel and chat destinations.
        // Keep legacy MessageCard available only for channel workflows.
        if ($webhook->destination_type === 'channel' && $webhook->message_format === 'message_card') {
            return [
                '@type' => 'MessageCard',
                '@context' => 'https://schema.org/extensions',
                'summary' => $title,
                'themeColor' => '2563EB',
                'title' => $title,
                'text' => $message,
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
                    'body' => array_values(array_filter([
                        $title !== '' ? ['type' => 'TextBlock', 'text' => $title, 'weight' => 'Bolder', 'size' => 'Large', 'wrap' => true] : null,
                        ['type' => 'TextBlock', 'text' => $message, 'wrap' => true],
                    ])),
                ],
            ]],
        ];
    }

    private function render(string $template, array $context): string
    {
        $normalizedContext = [];
        foreach ($context as $key => $value) {
            $normalizedKey = strtolower((string) $key);
            $normalizedContext[$normalizedKey] = $value;
            $normalizedContext[str_replace(['_', '-', '.'], '', $normalizedKey)] = $value;
        }

        return preg_replace_callback('/\{\s*([a-zA-Z0-9_.-]+)\s*\}/i', function (array $matches) use ($context, $normalizedContext): string {
            $key = strtolower(trim($matches[1]));
            $value = data_get($context, $matches[1]);

            if ($value === null) {
                $value = $normalizedContext[$key] ?? $normalizedContext[str_replace(['_', '-', '.'], '', $key)] ?? null;
            }

            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            return $value === null ? 'Not available' : (string) $value;
        }, $template) ?? $template;
    }

    private function defaultTitle(string $category): string
    {
        return $category === 'server_start' ? 'CCOD server startup' : 'CCOD Azure notification';
    }
}
