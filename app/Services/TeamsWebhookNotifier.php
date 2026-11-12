<?php

namespace App\Services;

use App\Models\TeamsWebhook;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeamsWebhookNotifier
{
    public function sendServerStart(array $context): void
    {
        $this->sendCategory('server_start', $context);
    }

    public function sendSyncStatus(array $context): void
    {
        $this->sendCategory('sync_status', $context);
    }

    public function sendTest(TeamsWebhook $webhook): bool
    {
        $testNow = now();
        $testTimestamp = $testNow->format('Y-m-d H:i:s T');
        $testStartedAt = $testNow->copy()->subMinutes(5)->format('Y-m-d H:i:s T');

        $context = $webhook->category === 'server_start'
            ? [
                'server_boot_time' => $testStartedAt,
                'iis_status' => 'RUNNING (TEST)',
                'iis_started_at' => $testStartedAt,
                'php_status' => 'RUNNING (TEST)',
                'php_started_at' => $testStartedAt,
                'mysql_status' => 'RUNNING (TEST)',
                'mysql_started_at' => $testStartedAt,
                'queue_status' => 'RUNNING (TEST)',
                'queue_started_at' => $testStartedAt,
            ]
            : [
                'overall_status' => 'SUCCESS (TEST)',
                'sync_started_at' => $testStartedAt,
                'duration' => '2 minutes (TEST)',
                'subscriptions_status' => 'SUCCESS',
                'resources_status' => 'SUCCESS',
                'costs_status' => 'SUCCESS',
                'security_status' => 'SUCCESS',
                'alerts_status' => 'SUCCESS',
                'web_urls_status' => 'SUCCESS',
                'completed_at' => $testTimestamp,
            ];

        return $this->deliver($webhook, $context);
    }

    private function sendCategory(string $category, array $context): void
    {
        TeamsWebhook::query()
            ->where('category', $category)
            ->where('enabled', true)
            ->orderBy('id')
            ->each(fn (TeamsWebhook $webhook) => $this->deliver($webhook, $context));
    }

    private function deliver(TeamsWebhook $webhook, array $context): bool
    {
        try {
            $url = $webhook->webhook_url;
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));

            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || $host === '' || filter_var($host, FILTER_VALIDATE_IP)) {
                throw new \RuntimeException('Webhook URL must use HTTPS and a valid hostname.');
            }

            $title = $this->render($webhook->title_template ?: $this->defaultTitle($webhook->category), $context);
            $message = $this->render($webhook->message_template, $context);
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
        return preg_replace_callback('/\{([a-zA-Z0-9_.-]+)\}/', function (array $matches) use ($context): string {
            $value = data_get($context, $matches[1]);

            return $value === null ? $matches[0] : (string) $value;
        }, $template) ?? $template;
    }

    private function defaultTitle(string $category): string
    {
        return $category === 'server_start' ? 'CCOD server startup' : 'CCOD Azure sync status';
    }
}
