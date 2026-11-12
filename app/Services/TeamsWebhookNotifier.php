<?php

namespace App\Services;

use App\Models\TeamsWebhook;
use App\Support\TeamsWebhookEvents;
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
        $this->dispatch('azure.sync.completed', $context);
    }

    /**
     * Dispatch a named CCOD event to every enabled destination subscribed to it.
     * Producers do not know about categories, templates, URLs or message formats.
     */
    public function dispatch(string $eventKey, array $context = []): int
    {
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
        $eventKey = $webhook->event_keys[0] ?? 'server.started';
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
        foreach ($conditions as $key => $expected) {
            $actual = data_get($context, $key);
            $results[] = $actual !== null && mb_strtolower((string) $actual) === mb_strtolower((string) $expected);
        }

        return ($webhook->condition_mode ?? 'all') === 'any'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
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
        if ($webhook->destination_type === 'chat') {
            return ['text' => trim(($title !== '' ? "**{$title}**\n\n" : '').$message)];
        }

        if ($webhook->message_format === 'message_card') {
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
