<?php

namespace App\Jobs;

use App\Models\TeamsWebhook;
use App\Services\TeamsWebhookNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class DeliverTeamsWebhookNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Three total attempts, with increasing delays between attempts.
     */
    public int $tries = 3;

    public int $timeout = 30;

    public int $maxExceptions = 3;

    public function __construct(
        public readonly int $webhookId,
        public readonly string $eventKey,
        public readonly array $context = [],
    ) {
        $this->afterCommit();
    }

    /**
     * Retry after 10 seconds, then 60 seconds.
     */
    public function backoff(): array
    {
        return [10, 60];
    }

    public function handle(TeamsWebhookNotifier $notifier): void
    {
        $webhook = TeamsWebhook::query()->find($this->webhookId);

        // A destination may have been deleted or disabled while this job waited.
        if (! $webhook || ! $webhook->enabled) {
            return;
        }

        $notifier->deliverQueuedRule($webhook, $this->eventKey, $this->context);
    }

    /**
     * Called by Laravel only after the job has exhausted its attempts.
     */
    public function failed(?Throwable $exception): void
    {
        $webhook = TeamsWebhook::query()->find($this->webhookId);

        if (! $webhook) {
            return;
        }

        $webhook->forceFill([
            'last_delivery_status' => 'failed',
            'last_sent_at' => now(),
            'last_error' => mb_substr(
                $exception?->getMessage() ?? 'Teams webhook delivery failed after all retry attempts.',
                0,
                2000,
            ),
        ])->save();
    }
}
