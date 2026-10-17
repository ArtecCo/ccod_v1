<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ClientPortalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Number of attempts allowed for a notification delivery job.
     */
    public int $tries = 3;

    /**
     * Maximum time a notification delivery job may run.
     */
    public int $timeout = 120;

    /**
     * Maximum number of unhandled exceptions before the job is failed.
     */
    public int $maxExceptions = 3;

    public function __construct(
        public readonly string $title,
        public readonly string $message,
        public readonly string $type = 'general',
        public readonly ?string $actionUrl = null,
        public readonly ?string $actionLabel = null,
        public readonly string $severity = 'info',
    ) {
        // Do not allow a worker to process this notification before an
        // enclosing database transaction has committed.
        $this->afterCommit();
    }

    /**
     * Seconds to wait between retry attempts.
     *
     * The delays increase so a temporary database problem does not cause
     * immediate repeated failures.
     */
    public function backoff(): array
    {
        return [10, 60, 300];
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'severity' => $this->severity,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
        ];
    }
}
