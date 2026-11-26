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
                (int) $rule->getKey(),
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

            // Pilot the subscription identity panel on Azure alert-detected cards only.
            // The existing severity banner remains in its original position.
            if ($eventKey === 'azure.alert.detected') {
                $payload = $this->insertAlertSubscriptionDetails($payload, $context);
            }

            // Keep every designed notification card on the SSL-renewed spacing standard.
            // Azure sync completion and failure retain their deliberately distinct layouts.
            if (! in_array($eventKey, ['sync.completed', 'azure.sync.failed'], true)) {
                $payload = $this->applyStandardCardSpacing($payload);
            }

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

    /**
     * Add subscription identity below the existing severity banner on the
     * Azure alert-detected pilot card only.
     */
    private function insertAlertSubscriptionDetails(array $payload, array $context): array
    {
        $body = $payload['attachments'][0]['content']['body'] ?? null;
        if (! is_array($body)) {
            return $payload;
        }

        $subscriptionName = trim((string) ($context['subscription_name'] ?? ''));
        $subscriptionId = trim((string) ($context['subscription_id'] ?? ''));
        if ($subscriptionName === '' && $subscriptionId === '') {
            return $payload;
        }

        $items = [];

        if ($subscriptionName !== '') {
            $items[] = [
                'type' => 'TextBlock',
                'text' => $subscriptionName,
                'weight' => 'Bolder',
                'size' => 'Medium',
                'spacing' => 'None',
                'wrap' => true,
            ];
        }

        if ($subscriptionId !== '') {
            $items[] = [
                'type' => 'TextBlock',
                'text' => 'ID: '.$subscriptionId,
                'size' => 'Small',
                'isSubtle' => true,
                'spacing' => 'Small',
                'wrap' => true,
            ];
        }

        $panel = [
            'type' => 'Container',
            'style' => 'emphasis',
            'spacing' => 'Medium',
            'items' => $items,
        ];

        $hasSeverityBanner = isset($body[0])
            && ($body[0]['type'] ?? null) === 'Container'
            && in_array($body[0]['style'] ?? null, ['attention', 'warning'], true);
        $insertAt = $hasSeverityBanner ? 1 : 0;
        array_splice($body, $insertAt, 0, [$panel]);

        $payload['attachments'][0]['content']['body'] = $body;

        return $payload;
    }

    /**
     * Apply the spacing hierarchy used by the SSL certificate renewed card
     * to the top-level content of every other Adaptive Card notification.
     */
    private function applyStandardCardSpacing(array $payload): array
    {
        $bodyPath = ['attachments', 0, 'content', 'body'];
        if (! isset($payload['attachments'][0]['content']['body'])
            || ! is_array($payload['attachments'][0]['content']['body'])) {
            return $payload;
        }

        $body = $payload['attachments'][0]['content']['body'];

        // Generic event cards contain their details as Markdown in one TextBlock.
        // Convert labelled lines to FactSet rows so they use the same readable
        // label/value spacing as the SSL-renewed reference card.
        foreach ($body as $index => $element) {
            if (($element['type'] ?? null) !== 'TextBlock' || $index < 2) {
                continue;
            }

            $text = (string) ($element['text'] ?? '');
            $lines = preg_split('/\\r?\\n/', $text) ?: [];
            $facts = [];
            $testNote = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                if (preg_match('/^\\*\\*(.+?):\\*\\*\\s*(.*)$/u', $line, $matches) === 1) {
                    $facts[] = [
                        'title' => trim($matches[1]),
                        'value' => trim($matches[2]),
                    ];
                } else {
                    $testNote[] = trim($line, "*_ ");
                }
            }

            if ($facts === []) {
                continue;
            }

            $replacement = [[
                'type' => 'FactSet',
                'spacing' => 'Small',
                'facts' => $facts,
            ]];

            if ($testNote !== []) {
                $replacement[] = [
                    'type' => 'TextBlock',
                    'text' => implode("\\n", $testNote),
                    'size' => 'Small',
                    'isSubtle' => true,
                    'wrap' => true,
                    'spacing' => 'Small',
                ];
            }

            array_splice($body, $index, 1, $replacement);
            break;
        }

        foreach ($body as $index => &$element) {
            if (! is_array($element)) {
                continue;
            }

            // Match the SSL-renewed card: banner, event heading, then details.
            $element['spacing'] = match ($index) {
                0 => 'None',
                1 => 'Medium',
                default => 'Small',
            };
        }
        unset($element);

        $payload['attachments'][0]['content']['body'] = $body;

        return $payload;
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

        if ($eventKey === 'web_url.ssl.expiring') {
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
            $title !== '' ? ['type' => 'TextBlock', 'text' => $title, 'weight' => 'Bolder', 'size' => 'Large', 'wrap' => true, 'spacing' => 'Medium'] : null,
            $message !== '' ? ['type' => 'TextBlock', 'text' => $message, 'wrap' => true, 'spacing' => 'Small'] : null,
        ]));

        $severityCategory = match (true) {
            in_array($eventKey, ['azure.security_score.decreased', 'azure.security_score.improved'], true) => 'Security score',
            in_array($eventKey, ['azure.alert.detected', 'azure.alert.resolved'], true) => 'Alert',
            in_array($eventKey, ['azure.recommendation.detected', 'azure.recommendation.removed'], true) => 'Security recommendation',
            in_array($eventKey, ['web_url.ssl.expiring', 'web_url.ssl.expired'], true) => 'SSL CERTIFICATE',
            default => null,
        };

        if ($severityCategory !== null) {
            $improvement = $eventKey === 'azure.security_score.improved';
            $severityValue = strtoupper((string) ($context['severity'] ?? ''));

            if ($severityCategory === 'Security score' && ! $improvement) {
                // Security Score decrease cards must reflect the configured
                // classification range. Never infer Warning/Critical from a
                // hard-coded score threshold or invent a fallback severity.
                $classification = match ($severityValue) {
                    'REMINDER' => ['label' => 'REMINDER', 'style' => 'warning', 'color' => 'Warning'],
                    'ATTENTION' => ['label' => 'WARNING', 'style' => 'warning', 'color' => 'Warning'],
                    'WARNING' => ['label' => 'ATTENTION', 'style' => 'attention', 'color' => 'Attention'],
                    'CRITICAL' => ['label' => 'CRITICAL', 'style' => 'attention', 'color' => 'Attention'],
                    default => null,
                };
            } elseif ($severityCategory === 'SSL CERTIFICATE') {
                $classification = $eventKey === 'web_url.ssl.expired'
                    ? ['label' => 'CRITICAL', 'style' => 'attention', 'color' => 'Attention']
                    : match ($severityValue) {
                        'REMINDER' => ['label' => 'REMINDER', 'style' => 'warning', 'color' => 'Warning'],
                        'ATTENTION' => ['label' => 'WARNING', 'style' => 'warning', 'color' => 'Warning'],
                        'WARNING' => ['label' => 'ATTENTION', 'style' => 'attention', 'color' => 'Attention'],
                        'CRITICAL' => ['label' => 'CRITICAL', 'style' => 'attention', 'color' => 'Attention'],
                        default => null,
                    };
            } elseif ($eventKey === 'azure.alert.resolved') {
                $classification = [
                    'label' => 'RESOLVED - Alert',
                    'style' => 'good',
                    'color' => 'Good',
                ];
            } else {
                $classification = match ($severityValue) {
                    'SEV0', '0' => ['label' => 'CRITICAL - Sev 0 Alert', 'style' => 'attention', 'color' => 'Attention'],
                    'SEV1', '1' => ['label' => 'WARNING - Sev 1 Alert', 'style' => 'attention', 'color' => 'Attention'],
                    'SEV2', '2' => ['label' => 'WARNING - Sev 2 Alert', 'style' => 'attention', 'color' => 'Attention'],
                    'SEV3', '3' => ['label' => 'ATTENTION - Sev 3 Alert', 'style' => 'warning', 'color' => 'Warning'],
                    'SEV4', '4' => ['label' => 'ATTENTION - Sev 4 Alert', 'style' => 'warning', 'color' => 'Warning'],
                    default => null,
                };
            }

            if ($improvement || $classification !== null) {
                $body[] = [
                    'type' => 'Container',
                    'style' => $improvement ? 'good' : $classification['style'],
                    'bleed' => true,
                    'spacing' => 'None',
                    'items' => [[
                        'type' => 'TextBlock',
                        'text' => $improvement
                            ? 'IMPROVEMENT - Security Score'
                            : ($severityCategory === 'Alert' ? $classification['label'] : ($classification['label'].' - '.$severityCategory)),
                        'weight' => 'Bolder',
                        'size' => 'Large',
                        'color' => $improvement ? 'Good' : $classification['color'],
                        'horizontalAlignment' => 'Center',
                        'wrap' => false,
                        'spacing' => 'Small',
                    ]],
                ];
                // Keep the existing banner position and Adaptive Card structure.
                $banner = array_pop($body);
                array_unshift($body, $banner);
            }
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
