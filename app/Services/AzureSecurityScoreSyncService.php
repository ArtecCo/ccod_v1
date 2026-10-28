<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use Illuminate\Support\Carbon;
use RuntimeException;

class AzureSecurityScoreSyncService
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
                'https://management.azure.com/subscriptions/%s/providers/Microsoft.Security/secureScores?api-version=%s',
                rawurlencode($subscription),
                self::API_VERSION,
            );

            $response = $this->azure->get($url)->throw();
            $payload = $response->json();
            if (! is_array($payload) || ! is_array($payload['value'] ?? null)) {
                throw new RuntimeException("Invalid secure score response for subscription {$subscription}.");
            }

            foreach ($payload['value'] as $score) {
                if (! is_array($score)) {
                    continue;
                }

                $stats['discovered']++;
                $attributes = $this->attributes($score, $subscription);
                $capturedAt = Carbon::now();

                $existing = AzureSecurityScore::query()
                    ->where('subscription_id', $subscription)
                    ->where('initiative_name', $attributes['initiative_name'])
                    ->whereBetween('captured_at', [$capturedAt->copy()->startOfMinute(), $capturedAt->copy()->endOfMinute()])
                    ->first();

                if ($existing === null) {
                    AzureSecurityScore::query()->create([
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
            }
        }

        return $stats;
    }

    private function attributes(array $score, string $subscriptionId): array
    {
        $properties = is_array($score['properties'] ?? null) ? $score['properties'] : [];
        $details = is_array($properties['score'] ?? null) ? $properties['score'] : [];

        return [
            'subscription_id' => $subscriptionId,
            'initiative_name' => is_string($properties['displayName'] ?? null) && $properties['displayName'] !== ''
                ? $properties['displayName']
                : (is_string($score['name'] ?? null) ? $score['name'] : null),
            'current_score' => (float) ($details['current'] ?? 0),
            'max_score' => (float) ($details['max'] ?? 0),
            'percentage_score' => (float) ($details['percentage'] ?? 0),
            'weight' => (int) ($properties['weight'] ?? 0),
        ];
    }
}
