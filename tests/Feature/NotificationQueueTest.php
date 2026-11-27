<?php

namespace Tests\Feature;

use App\Jobs\DeliverTeamsWebhookNotification;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use App\Support\NotificationEventCatalog;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class NotificationQueueTest extends TestCase
{
    public function test_client_portal_notifications_are_queued_for_active_recipients(): void
    {
        Queue::fake();

        $user = User::query()->create([
            'name' => 'Queue Test User',
            'email' => 'queue-test@example.invalid',
            'password' => 'test-password',
            'is_active' => true,
        ]);

        $auditLogger = Mockery::mock(AuditLogger::class);
        $auditLogger->shouldReceive('log')->once();

        $service = new NotificationService($auditLogger);

        $queued = $service->send(
            title: 'Queue test',
            message: 'This notification must be queued.',
            userIds: [$user->id],
        );

        $this->assertSame(1, $queued);
        Queue::assertPushed(SendQueuedNotifications::class, function (SendQueuedNotifications $job) use ($user): bool {
            return collect($job->notifiables)->contains(fn ($notifiable): bool => (int) $notifiable->getKey() === (int) $user->id)
                && $job->notification instanceof ClientPortalNotification;
        });
    }

    public function test_notification_delivery_types_implement_laravel_queue_contract(): void
    {
        $this->assertContains(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            class_implements(ClientPortalNotification::class),
        );
        $this->assertContains(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            class_implements(DeliverTeamsWebhookNotification::class),
        );
    }

    public function test_ssl_expiry_event_exposes_classified_severity_not_classification_thresholds(): void
    {
        $fields = NotificationEventCatalog::event('web_url.ssl.expiring')['fields'];

        $this->assertArrayNotHasKey('ssl_reminder_threshold_days', $fields);
        $this->assertArrayHasKey('severity', $fields);
        $this->assertSame('enum', $fields['severity']['type']);
        $this->assertArrayHasKey('ATTENTION', $fields['severity']['values']);
        $this->assertArrayHasKey('WARNING', $fields['severity']['values']);
        $this->assertArrayHasKey('CRITICAL', $fields['severity']['values']);
    }

    public function test_security_score_decrease_event_exposes_classified_severity_condition(): void
    {
        $fields = NotificationEventCatalog::event('azure.security_score.decreased')['fields'];

        $this->assertArrayHasKey('severity', $fields);
        $this->assertSame('enum', $fields['severity']['type']);
        $this->assertArrayHasKey('ATTENTION', $fields['severity']['values']);
        $this->assertArrayHasKey('WARNING', $fields['severity']['values']);
        $this->assertArrayHasKey('CRITICAL', $fields['severity']['values']);
    }

    public function test_security_score_improvement_event_remains_unclassified(): void
    {
        $fields = NotificationEventCatalog::event('azure.security_score.improved')['fields'];

        $this->assertArrayNotHasKey('severity', $fields);
    }

    public function test_security_score_webhook_values_use_percentage_points(): void
    {
        $formatted = app(\App\Support\WebhookMessageFormatter::class)->format(
            'azure.security_score.improved',
            [
                'previous_score' => 70.0,
                'current_score' => 79.0,
                'score_delta' => 9.0,
            ],
        );

        $this->assertStringContainsString('70.00 %', $formatted['message']);
        $this->assertStringContainsString('79.00 %', $formatted['message']);
        $this->assertStringContainsString('9.00 percentage points', $formatted['message']);
    }

}
