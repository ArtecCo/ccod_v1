<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureAlert;
use App\Models\AzureAlertDetail;
use App\Models\AzureSubscription;
use App\Services\NotificationRuleService;
use Illuminate\Support\Carbon;
use RuntimeException;
use Throwable;

class AzureAlertSyncService
{
    private const API_VERSION = '2024-04-01';
    private const PAGE_SIZE = 1000;
    private const RETENTION_DAYS = 30;

    public function __construct(
        private readonly AzureSubscriptionSyncService $azure,
        private readonly SubscriptionModuleSyncTracker $syncTracker,
    ) {
    }

    public function sync(?string $subscriptionId = null): array
    {
        $subscriptions = AzureSubscription::query()
            ->when($subscriptionId !== null, fn ($query) => $query->where('subscription_id', $subscriptionId))
            ->pluck('subscription_id')
            ->filter(fn (mixed $id): bool => is_string($id) && $id !== '')
            ->values()
            ->all();

        $stats = [
            'subscriptions' => count($subscriptions),
            'discovered' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'resolved' => 0,
            'deleted' => 0,
        ];

        foreach ($subscriptions as $id) {
            $this->syncTracker->begin('alerts', [$id]);

            try {
                $result = $this->syncSubscription($id);
                $this->syncTracker->succeed('alerts', [$id]);
            } catch (Throwable $exception) {
                $this->syncTracker->fail('alerts', [$id], $exception->getMessage());
                throw $exception;
            }

            foreach ($result as $key => $value) {
                $stats[$key] += $value;
            }
        }

        return $stats;
    }

    private function syncSubscription(string $subscriptionId): array
    {
        $stats = ['discovered' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'resolved' => 0, 'deleted' => 0];
        $skipToken = null;

        do {
            $payload = [
                'subscriptions' => [$subscriptionId],
                'query' => <<<'KQL'
alertsmanagementresources
| where type =~ "microsoft.alertsmanagement/alerts"
| where tostring(properties.essentials.monitorCondition) in~ ("Fired", "Resolved")
| where todatetime(properties.essentials.startDateTime) > ago(30d)
| project
    alertId = id,
    alertProperties = properties,
    alertName = name,
    severity = tostring(properties.essentials.severity),
    affectedResource = tostring(properties.essentials.targetResourceName),
    targetResourceType = tostring(properties.essentials.targetResourceType),
    alertCondition = tostring(properties.essentials.monitorCondition),
    userResponse = tostring(properties.essentials.alertState),
    monitorService = tostring(properties.essentials.monitorService),
    signalType = tostring(properties.essentials.signalType),
    fireTime = todatetime(properties.essentials.startDateTime),
    lastModifiedTime = todatetime(properties.essentials.lastModifiedDateTime),
    subscriptionId,
    targetResourceGroup = tostring(properties.essentials.targetResourceGroup),
    suppressionStatus = tostring(properties.essentials.suppressionStatus)
| order by alertId asc
KQL,
                'options' => [
                    '$top' => self::PAGE_SIZE,
                    'resultFormat' => 'objectArray',
                ],
            ];

            if ($skipToken !== null) {
                $payload['options']['$skipToken'] = $skipToken;
            }

            $response = $this->azure->post(
                'https://management.azure.com/providers/Microsoft.ResourceGraph/resources?api-version=' . self::API_VERSION,
                $payload,
            )->throw();

            $page = $response->json();
            if (! is_array($page) || ! is_array($page['data'] ?? null)) {
                throw new RuntimeException('Invalid Azure alert Resource Graph response.');
            }

            foreach ($page['data'] as $alert) {
                if (! is_array($alert) || ! is_string($alert['alertId'] ?? null) || $alert['alertId'] === '') {
                    continue;
                }

                $stats['discovered']++;
                $result = $this->syncOne($alert, $subscriptionId);
                $stats[$result]++;
                if ($result !== 'resolved') {
                    $this->syncDetails($alert);
                } else {
                    $stats['deleted']++;
                }
            }

            $next = $page['$skipToken'] ?? null;
            $skipToken = is_string($next) && $next !== '' ? $next : null;
        } while ($skipToken !== null);

        // Keep Fired alerts for Azure's full 30-day alert retention window.
        // Resolved alerts are removed only after their resolution event is queued.
        $expired = AzureAlert::query()
            ->where('subscription_id', $subscriptionId)
            ->whereNotNull('fire_time')
            ->where('fire_time', '<', now()->subDays(self::RETENTION_DAYS));

        $stats['deleted'] += (clone $expired)->count();
        $expired->delete();

        return $stats;
    }

    private function syncDetails(array $alert): void
    {
        $alertId = (string) ($alert['alertId'] ?? '');
        if ($alertId === '') {
            return;
        }

        $record = AzureAlert::query()->where('azure_alert_id', $alertId)->first();
        if ($record === null) {
            return;
        }

        $properties = is_array($alert['alertProperties'] ?? null) ? $alert['alertProperties'] : [];
        AzureAlertDetail::query()->updateOrCreate(
            ['alert_id' => $record->id],
            [
                'raw_properties' => $properties ?: null,
                'fetched_at' => now(),
            ],
        );
    }

    private function syncOne(array $alert, string $subscriptionId): string
    {
        $alertId = (string) $alert['alertId'];
        $attributes = [
            'azure_alert_id' => $alertId,
            'name' => $this->stringOrNull($alert['alertName'] ?? null),
            'severity' => $this->stringOrNull($alert['severity'] ?? null),
            'affected_resource' => $this->stringOrNull($alert['affectedResource'] ?? null),
            'target_resource_type' => $this->stringOrNull($alert['targetResourceType'] ?? null),
            'alert_condition' => $this->stringOrNull($alert['alertCondition'] ?? null),
            'user_response' => $this->stringOrNull($alert['userResponse'] ?? null),
            'monitor_service' => $this->stringOrNull($alert['monitorService'] ?? null),
            'signal_type' => $this->stringOrNull($alert['signalType'] ?? null),
            'fire_time' => $this->dateOrNull($alert['fireTime'] ?? null),
            'last_modified_time' => $this->dateOrNull($alert['lastModifiedTime'] ?? null),
            'subscription_id' => $subscriptionId,
            'target_resource_group' => $this->stringOrNull($alert['targetResourceGroup'] ?? null),
            'suppression_status' => $this->stringOrNull($alert['suppressionStatus'] ?? null),
        ];

        $existing = AzureAlert::query()->where('azure_alert_id', $alertId)->first();

        if ($existing === null) {
            $legacyId = hash(
                'sha256',
                $subscriptionId.'|'.
                ($alert['alertName'] ?? '').'|'.
                ($alert['affectedResource'] ?? '').'|'.
                ($alert['fireTime'] ?? '')
            );

            $existing = AzureAlert::query()->where('azure_alert_id', $legacyId)->first();
        }

        if ($existing === null) {
            $existing = AzureAlert::query()->create($attributes);

            if (strcasecmp((string) $attributes['alert_condition'], 'Resolved') === 0) {
                $this->triggerResolvedEvent($alertId, $attributes, $subscriptionId);
                $existing->delete();

                return 'resolved';
            }

            $context = $this->notificationContext($alertId, $attributes, $subscriptionId);

            try {
                app(NotificationRuleService::class)->trigger('azure.alert.detected', $context);
            } catch (Throwable $exception) {
                report($exception);
            }

            return 'created';
        }

        $changed = false;
        foreach ($attributes as $key => $value) {
            if ($existing->getAttribute($key) != $value) {
                $changed = true;
                break;
            }
        }

        $existing->forceFill($attributes)->saveQuietly();

        if (strcasecmp((string) $attributes['alert_condition'], 'Resolved') === 0) {
            // If queuing fails, the row remains Resolved and the next sync retries it.
            $this->triggerResolvedEvent($alertId, $attributes, $subscriptionId);
            $existing->delete();

            return 'resolved';
        }

        return $changed ? 'updated' : 'unchanged';
    }

    private function triggerResolvedEvent(string $alertId, array $attributes, string $subscriptionId): void
    {
        app(NotificationRuleService::class)->trigger(
            'azure.alert.resolved',
            $this->notificationContext($alertId, $attributes, $subscriptionId),
            true,
        );
    }

    private function notificationContext(string $alertId, array $attributes, string $subscriptionId): array
    {
        return [
            'alert_id' => $alertId,
            'alert_name' => $attributes['name'],
            'severity' => $attributes['severity'],
            'status' => $attributes['alert_condition'],
            'resource_id' => null,
            'resource_name' => $attributes['affected_resource'],
            'subscription_id' => $subscriptionId,
            'subscription_name' => AzureSubscription::query()->where('subscription_id', $subscriptionId)->value('display_name'),
            'occurred_at' => $attributes['last_modified_time']?->toIso8601String() ?? now()->toIso8601String(),
        ];
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
