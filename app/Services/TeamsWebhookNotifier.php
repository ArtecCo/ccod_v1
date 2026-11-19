<?php

namespace App\Services;

use App\Models\NotificationRule;
use App\Models\TeamsWebhook;
use App\Support\NotificationEventCatalog;
use App\Support\WebhookMessageFormatter;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeamsWebhookNotifier
{
    public function __construct(
        private readonly WebhookMessageFormatter $formatter,
    ) {}

    /**
     * Deliver a rule using the message format defined in application code.
     */
    public function deliverRule(TeamsWebhook $webhook, string $eventKey, array $context = []): bool
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $context = array_merge($context, [
            'event_key' => $eventKey,
            'event_label' => NotificationEventCatalog::event($eventKey)['label'] ?? $eventKey,
        ]);

        if (! $webhook->enabled) {
            return false;
        }

        return $this->deliver($webhook, $eventKey, $context);
    }

    public function sendRuleTest(NotificationRule $rule): bool
    {
        $rule->loadMissing('webhook');
        $webhook = $rule->webhook;

        if (! $webhook || ! $webhook->enabled) {
            return false;
        }

        $eventKey = (string) ($rule->getRawOriginal('event_key') ?: $rule->event_key);
        $context = NotificationEventCatalog::sampleContext($eventKey);
        $context['is_test'] = true;

        return $this->deliverRule($webhook, $eventKey, $context);
    }

    private function deliver(TeamsWebhook $webhook, string $eventKey, array $context): bool
    {
        try {
            $url = $webhook->webhook_url;
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \\RuntimeException('Webhook URL must use HTTPS and a valid hostname.');
            }

            $message = $this->formatter->format($eventKey, $context);
            $payload = $this->payload($webhook, $message['title'], $message['message'], $eventKey, $context);

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->withOptions(['allow_redirects' => false])
                ->post($url, $payload);

            if (! $response->successful()) {
                throw new RuntimeException('Webhook endpoint returned HTTP '.$response->status().'.');
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

    private function payload(TeamsWebhook $webhook, string $title, string $message, string $eventKey, array $context): array
    {
        if ($webhook->destination_type === 'generic') {
            return [
                'event_key' => $eventKey,
                'title' => $title,
                'message' => $message,
                'context' => $context,
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
                        $message !== '' ? ['type' => 'TextBlock', 'text' => $message, 'wrap' => true] : null,
                    ])),
                ],
            ]],
        ];
    }
}
