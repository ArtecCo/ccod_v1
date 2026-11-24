<?php

namespace App\Services;

use App\Jobs\DeliverTeamsWebhookNotification;
use App\Models\SslReminderRule;
use App\Models\SubscriptionWebUrl;
use App\Models\TeamsWebhook;
use Illuminate\Support\Facades\DB;
use Throwable;

class SslReminderService
{
    /**
     * Dispatch each configured threshold once for a given certificate.
     * When a certificate is first observed already inside several thresholds,
     * only the closest (most urgent) threshold is sent.
     */
    public function check(SubscriptionWebUrl $webUrl, ?int $previousDaysRemaining): int
    {
        if (! $webUrl->is_active || ! $webUrl->ssl_expires_at || $webUrl->ssl_days_remaining === null) {
            return 0;
        }

        $daysRemaining = (int) $webUrl->ssl_days_remaining;
        if ($daysRemaining < 0) {
            return 0;
        }

        $rules = SslReminderRule::query()
            ->where('enabled', true)
            ->where('threshold_days', '>=', $daysRemaining)
            ->orderBy('threshold_days')
            ->get();

        if ($rules->isEmpty()) {
            return 0;
        }

        if ($previousDaysRemaining === null) {
            // Avoid a burst of historical reminders when a URL is first checked.
            $rules = $rules->take(1);
        } else {
            $rules = $rules->filter(
                fn (SslReminderRule $rule): bool => $previousDaysRemaining > $rule->threshold_days,
            );
        }

        $dispatched = 0;
        foreach ($rules as $rule) {
            $inserted = DB::table('ssl_reminder_deliveries')->insertOrIgnore([
                'ssl_reminder_rule_id' => $rule->id,
                'subscription_web_url_id' => $webUrl->id,
                'certificate_expires_at' => $webUrl->ssl_expires_at->toDateTimeString(),
                'dispatched_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($inserted !== 1) {
                continue;
            }

            $webhookIds = array_values(array_unique(array_filter(
                $rule->webhook_ids ?? [],
                static fn ($id): bool => is_numeric($id),
            )));
            $webhooks = TeamsWebhook::query()
                ->where('enabled', true)
                ->whereKey($webhookIds)
                ->get();

            $context = [
                'web_url_id' => (int) $webUrl->id,
                'url' => (string) $webUrl->url,
                'domain' => (string) (parse_url($webUrl->url, PHP_URL_HOST) ?: $webUrl->url),
                'certificate_expires_at' => $webUrl->ssl_expires_at->toIso8601String(),
                'days_until_expiry' => $daysRemaining,
                'ssl_reminder_threshold_days' => (int) $rule->threshold_days,
                'ssl_reminder_severity' => (string) $rule->severity,
                'occurred_at' => now()->toIso8601String(),
            ];

            foreach ($webhooks as $webhook) {
                try {
                    DeliverTeamsWebhookNotification::dispatch(
                        (int) $webhook->id,
                        'web_url.ssl.expiring',
                        $context,
                    )->afterCommit();
                    $dispatched++;
                } catch (Throwable $exception) {
                    // Release the deduplication key when queueing itself fails so
                    // the next URL check can safely retry this threshold.
                    DB::table('ssl_reminder_deliveries')
                        ->where('ssl_reminder_rule_id', $rule->id)
                        ->where('subscription_web_url_id', $webUrl->id)
                        ->where('certificate_expires_at', $webUrl->ssl_expires_at->toDateTimeString())
                        ->delete();
                    report($exception);
                }
            }
        }

        return $dispatched;
    }
}
