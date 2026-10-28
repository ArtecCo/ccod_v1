<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSecurityScoreControl;
use App\Models\AzureSubscription;
use Illuminate\Support\Carbon;
use RuntimeException;

class AzureSecurityScoreControlSyncService
{
    private const API_VERSION = '2020-01-01';

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

        $stats = ['subscriptions' => count($subscriptions), 'discovered' => 0, 'created' => 0, 'updated' => 0];

        foreach ($subscriptions as $subscription) {
            $url = sprintf(
                'https://management.azure.com/subscriptions/%s/providers/Microsoft.Security/secureScores/ascScore/secureScoreControls?api-version=%s',
                rawurlencode($subscription),
                self::API_VERSION,
            );

            while ($url !== null) {
                $response = $this->azure->get($url)->throw();
                $payload = $response->json();
                if (! is_array($payload) || ! is_array($payload['value'] ?? null)) {
                    throw new RuntimeException("Invalid secure score control response for subscription {$subscription}.");
                }

                foreach ($payload['value'] as $control) {
                    if (! is_array($control)) {
                        continue;
                    }

                    $controlId = is_string($control['name'] ?? null) ? trim($control['name']) : '';
                    $properties = is_array($control['properties'] ?? null) ? $control['properties'] : [];
                    $displayName = is_string($properties['displayName'] ?? null) ? trim($properties['displayName']) : '';

                    if ($controlId === '' || $displayName === '') {
                        continue;
                    }

                    $score = is_array($properties['score'] ?? null) ? $properties['score'] : [];
                    $capturedAt = Carbon::now();
                    $attributes = [
                        'subscription_id' => $subscription,
                        'control_id' => $controlId,
                        'display_name' => $displayName,
                        'current_score' => (float) ($score['current'] ?? 0),
                        'max_score' => (float) ($score['max'] ?? 0),
                        'percentage_score' => (float) ($score['percentage'] ?? 0),
                        'healthy_resource_count' => (int) ($properties['healthyResourceCount'] ?? 0),
                        'unhealthy_resource_count' => (int) ($properties['unhealthyResourceCount'] ?? 0),
                        'not_applicable_resource_count' => (int) ($properties['notApplicableResourceCount'] ?? 0),
                        'weight' => (int) ($properties['weight'] ?? 0),
                    ];

                    $existing = AzureSecurityScoreControl::query()
                        ->where('subscription_id', $subscription)
                        ->where('control_id', $controlId)
                        ->whereBetween('captured_at', [$capturedAt->copy()->startOfMinute(), $capturedAt->copy()->endOfMinute()])
                        ->first();

                    if ($existing === null) {
                        AzureSecurityScoreControl::query()->create([
                            ...$attributes,
                            'captured_at' => $capturedAt,
                            'synced_at' => $capturedAt,
                        ]);
                        $stats['created']++;
                    } else {
                        $existing->forceFill([
                            ...$attributes,
                            'captured_at' => $capturedAt,
                            'synced_at' => $capturedAt,
                        ])->saveQuietly();
                        $stats['updated']++;
                    }

                    $stats['discovered']++;
                }

                $next = $payload['nextLink'] ?? null;
                $url = is_string($next) && $next !== '' ? $next : null;
            }
        }

        return $stats;
    }
}
