<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureAlert;
use App\Models\AzureSubscription;
use Illuminate\Support\Carbon;
use RuntimeException;

class AzureAlertSyncService
{
    private const API_VERSION = '2024-04-01';
    private const PAGE_SIZE = 1000;
    private const LOOKBACK_HOURS = 24;

    public function __construct(
        private readonly AzureSubscriptionSyncService $azure,
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
            'deleted' => 0,
        ];

        foreach ($subscriptions as $id) {
            $result = $this->syncSubscription($id);

            foreach ($result as $key => $value) {
                $stats[$key] += $value;
            }
        }

        return $stats;
    }

    private function syncSubscription(string $subscriptionId): array
    {
        $stats = ['discovered' => 0, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'deleted' => 0];
        $seen = [];
        $skipToken = null;

        do {
            $payload = [
                'subscriptions' => [$subscriptionId],
                'query' => <<<'KQL'
alertsmanagementresources
| where type =~ "microsoft.alertsmanagement/alerts"
| where tostring(properties.essentials.monitorCondition) =~ "Fired"
| where todatetime(properties.essentials.startDateTime) > ago(24h)
| project
    alertId = id,
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
                $seen[] = $alert['alertId'];
                $stats[$this->syncOne($alert, $subscriptionId)]++;
            }

            $next = $page['$skipToken'] ?? null;
            $skipToken = is_string($next) && $next !== '' ? $next : null;
        } while ($skipToken !== null);

        $query = AzureAlert::query()->where('subscription_id', $subscriptionId);
        $stats['deleted'] = $seen === []
            ? $query->count()
            : (clone $query)->whereNotIn('azure_alert_id', $seen)->count();

        if ($seen === []) {
            $query->delete();
        } else {
            $query->whereNotIn('azure_alert_id', $seen)->delete();
        }

        return $stats;
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
            AzureAlert::query()->create($attributes);
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

        return $changed ? 'updated' : 'unchanged';
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
        } catch (\Throwable) {
            return null;
        }
    }
}
