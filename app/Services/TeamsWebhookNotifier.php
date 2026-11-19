<?php

namespace App\Services;

use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookTemplate;
use App\Support\NotificationEventCatalog;
use App\Support\TeamsWebhookEvents;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeamsWebhookNotifier
{
    /**
     * Deliver one configured notification rule to its selected destination.
     */
    public function deliverRule(TeamsWebhook $webhook, string $eventKey, array $context = [], ?TeamsWebhookTemplate $templateOverride = null): bool
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $context = array_merge($context, [
            'event_key' => $eventKey,
            'event_label' => NotificationEventCatalog::event($eventKey)['label'] ?? $eventKey,
        ]);

        if (! $webhook->enabled) {
            return false;
        }

        return $this->deliver($webhook, $context, $templateOverride);
    }

    public function sendRuleTest(\App\Models\NotificationRule $rule): bool
    {
        $rule->loadMissing(['webhook', 'template']);
        $webhook = $rule->webhook;

        if (! $webhook || ! $webhook->enabled || ! $rule->template) {
            return false;
        }

        $eventKey = $rule->getRawOriginal('event_key') ?: (string) $rule->event_key;
        $context = TeamsWebhookEvents::sampleContext($eventKey);
        $context['is_test'] = true;

        return $this->deliver($webhook, $context, $rule->template);
    }

    private function deliver(TeamsWebhook $webhook, array $context, ?TeamsWebhookTemplate $templateOverride = null): bool
    {
        try {
            $url = $webhook->webhook_url;
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException('Webhook URL must use HTTPS and a valid hostname.');
            }

            $template = $templateOverride;
            $titleTemplate = $template?->title_template ?: 'CCOD notification';
            $messageTemplate = $template?->message_template ?: '';
            $title = $this->render($titleTemplate, $context);
            $message = $this->render($messageTemplate, $context);
            $payload = $this->payload($webhook, $title, $message, $context);

            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->withOptions(['allow_redirects' => false])
                ->post($url, $payload);

            if (! $response->successful()) {
                throw new \RuntimeException('Webhook endpoint returned HTTP '.$response->status().'.');
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

    private function payload(TeamsWebhook $webhook, string $title, string $message, array $context): array
    {
        if ($webhook->destination_type === 'generic') {
            return [
                'event_key' => $context['event_key'] ?? null,
                'title' => $title,
                'message' => $message,
                'context' => $context,
            ];
        }

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
                        $message !== '' ? ['type' => 'TextBlock', 'text' => $message, 'wrap' => true] : null,
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

}
