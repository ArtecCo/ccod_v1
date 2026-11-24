<?php

namespace App\Services;

use App\Models\NotificationRule;
use App\Models\TeamsWebhook;
use App\Jobs\DeliverTeamsWebhookNotification;
use App\Support\NotificationEventCatalog;
use App\Support\WebhookMessageFormatter;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class TeamsWebhookNotifier
{
    public function __construct(
        private readonly WebhookMessageFormatter $formatter,
    ) {}

    /**
     * Deliver a queued rule using the message format defined in application code.
     *
     * Exceptions are deliberately allowed to escape so Laravel can retry the job.
     */
    public function deliverQueuedRule(TeamsWebhook $webhook, string $eventKey, array $context = []): void
    {
        $eventKey = NotificationEventCatalog::normalizeKey($eventKey);
        $context = array_merge($context, [
            'event_key' => $eventKey,
            'event_label' => NotificationEventCatalog::event($eventKey)['label'] ?? $eventKey,
        ]);

        if (! $webhook->enabled) {
            return;
        }

        $this->deliver($webhook, $eventKey, $context);
    }

    /**
     * Queue a test delivery; the Filament action reports that it was queued,
     * not that Teams has already accepted it.
     */
    public function queueRuleTest(NotificationRule $rule): bool
    {
        $rule->loadMissing(['webhook', 'webhooks']);
        $destinations = $rule->webhooks->isNotEmpty() ? $rule->webhooks : collect([$rule->webhook])->filter();
        $destinations = $destinations->where('enabled', true);

        if ($destinations->isEmpty()) {
            return false;
        }

        $eventKey = (string) ($rule->getRawOriginal('event_key') ?: $rule->event_key);
        $context = NotificationEventCatalog::sampleContext($eventKey);
        $context['is_test'] = true;

        foreach ($destinations as $webhook) {
            DeliverTeamsWebhookNotification::dispatch(
                (int) $webhook->getKey(),
                $eventKey,
                $context,
            )->afterCommit();
        }

        return true;
    }

    private function deliver(TeamsWebhook $webhook, string $eventKey, array $context): bool
    {
        try {
            $url = $webhook->webhook_url;
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException('Webhook URL must use HTTPS and a valid hostname.');
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

            // Keep the latest failure visible while Laravel schedules a retry.
            $webhook->forceFill([
                'last_delivery_status' => 'retrying',
                'last_sent_at' => now(),
                'last_error' => mb_substr($exception->getMessage(), 0, 2000),
            ])->save();

            throw $exception;
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

        if ($eventKey === 'sync.completed') {
            return $this->formatter->syncCompletionCard($title, $context);
        }

        if ($eventKey === 'azure.sync.failed') {
            return $this->formatter->syncFailureCard($title, $context);
        }

        if ($eventKey === 'web_url.ssl.expiring' && isset($context['ssl_reminder_severity'])) {
            return app(\App\Support\SslReminderCardFormatter::class)->format($title, $context);
        }

        if ($eventKey === 'web_url.ssl.renewed') {
            return $this->formatter->webUrlSslRenewedCard($title, $context);
        }

        if (in_array($eventKey, ['web_url.down', 'web_url.recovered'], true)) {
            return $this->formatter->webUrlStatusCard($eventKey, $title, $context);
        }

        if ($eventKey === 'server.started') {
            return $this->formatter->serverStartedCard($title, $context);
        }

        $body = array_values(array_filter([
            $title !== '' ? ['type' => 'TextBlock', 'text' => $title, 'weight' => 'Bolder', 'size' => 'Large', 'wrap' => true] : null,
            $message !== '' ? ['type' => 'TextBlock', 'text' => $message, 'wrap' => true] : null,
        ]));

        $severityCategory = match (true) {
            in_array($eventKey, ['azure.security_score.decreased', 'azure.security_score.improved'], true) => 'Security score',
            in_array($eventKey, ['azure.alert.detected', 'azure.critical_alert.detected'], true) => 'Alert',
            in_array($eventKey, ['azure.recommendation.detected', 'azure.recommendation.removed'], true) => 'Security recommendation',
            in_array($eventKey, ['web_url.ssl.expiring', 'web_url.ssl.expired'], true) => 'SSL CERTIFICATE',
            default => null,
        };

        if ($severityCategory !== null) {
            $improvement = $eventKey === 'azure.security_score.improved';

            if ($severityCategory === 'Security score' && ! $improvement) {
                $critical = (float) ($context['current_score'] ?? 0) < 0.8;
            } elseif ($severityCategory === 'SSL') {
                $critical = $eventKey === 'web_url.ssl.expired';
            } else {
                $severityValue = strtoupper((string) ($context['severity'] ?? ''));
                $critical = in_array($severityValue, ['CRITICAL', 'HIGH', 'SEV0', 'SEV1', '0', '1'], true)
                    || $eventKey === 'azure.critical_alert.detected';
            }

            $body[] = [
                'type' => 'Container',
                'style' => $improvement ? 'good' : ($critical ? 'attention' : 'warning'),
                'bleed' => true,
                'spacing' => 'None',
                'items' => [[
                    'type' => 'TextBlock',
                    'text' => $improvement
                        ? 'IMPROVEMENT - Security Score'
                        : (($critical ? 'CRITICAL' : 'WARNING').' - '.$severityCategory),
                    'weight' => 'Bolder',
                    'size' => 'Large',
                    'color' => $improvement ? 'Good' : ($critical ? 'Attention' : 'Warning'),
                    'horizontalAlignment' => 'Center',
                    'wrap' => false,
                    'spacing' => 'Small',
                ]],
            ];
            // Keep the severity banner before the existing title and message.
            $banner = array_pop($body);
            array_unshift($body, $banner);
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
}
