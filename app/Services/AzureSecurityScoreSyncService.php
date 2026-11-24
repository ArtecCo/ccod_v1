<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Client\Response;
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

        $stats = ['subscriptions' => count($subscriptions), 'discovered' => 0, 'created' => 0, 'updated' => 0, 'deduplicated' => 0, 'pruned' => 0];

        foreach ($subscriptions as $subscription) {
            $url = sprintf(
                'https://management.azure.com/subscriptions/%s/providers/Microsoft.Security/secureScores?api-version=%s',
                rawurlencode($subscription),
                self::API_VERSION,
            );

            $response = $this->getWithGatewayRetry($url)->throw();
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
                $dayStart = $capturedAt->copy()->startOfDay();
                $dayEnd = $capturedAt->copy()->endOfDay();

                // Retain one snapshot per subscription and initiative per calendar day.
                // A later scan on the same day replaces the earlier snapshot.
                $notificationContext = null;

                DB::transaction(function () use ($subscription, $attributes, $capturedAt, $dayStart, $dayEnd, &$stats, &$notificationContext): void {
                    $dailySnapshots = AzureSecurityScore::query()
                        ->where('subscription_id', $subscription)
                        ->where('initiative_name', $attributes['initiative_name'])
                        ->whereBetween('captured_at', [$dayStart, $dayEnd])
                        ->orderByDesc('captured_at')
                        ->lockForUpdate()
                        ->get();
                    $existing = $dailySnapshots->first();
                    $previousScore = $existing?->percentage_score;

                    if ($previousScore === null) {
                        $previousScore = AzureSecurityScore::query()
                            ->where('subscription_id', $subscription)
                            ->where('initiative_name', $attributes['initiative_name'])
                            ->where('captured_at', '<', $dayStart)
                            ->orderByDesc('captured_at')
                            ->value('percentage_score');
                    }

                    if ($existing === null) {
                        AzureSecurityScore::query()->create([
                            ...$attributes,
                            'captured_at' => $capturedAt,
                            'synced_at' => $capturedAt,
                        ]);
                        $stats['created']++;
                    } else {
                        $duplicateIds = $dailySnapshots->skip(1)->pluck('id');
                        if ($duplicateIds->isNotEmpty()) {
                            $stats['deduplicated'] += AzureSecurityScore::query()
                                ->whereIn('id', $duplicateIds)
                                ->delete();
                        }
                        $existing->forceFill([
                            ...$attributes,
                            'captured_at' => $capturedAt,
                            'synced_at' => $capturedAt,
                        ])->saveQuietly();
                        $stats['updated']++;
                    }

                    $currentScore = (float) $attributes['percentage_score'];
                    if ($previousScore !== null && (float) $previousScore !== $currentScore) {
                        $notificationContext = [
                            'subscription_id' => $subscription,
                            'subscription_name' => AzureSubscription::query()->where('subscription_id', $subscription)->value('display_name'),
                            'previous_score' => (float) $previousScore,
                            'current_score' => $currentScore,
                            'score_delta' => $currentScore - (float) $previousScore,
                            'occurred_at' => $capturedAt->toIso8601String(),
                        ];

                        // Security score ranges classify decreases only. Improvements
                        // retain their existing unclassified event and card behavior.
                        if ($currentScore < (float) $previousScore) {
                            $classification = \\App\\Models\\AzureSecurityScoreClassification::query()
                                ->where('enabled', true)
                                ->where('minimum_score', '<=', $currentScore)
                                ->where('maximum_score', '>=', $currentScore)
                                ->orderBy('minimum_score')
                                ->first();

                            if ($classification !== null) {
                                $notificationContext['severity'] = strtoupper($classification->severity);
                            }
                        }
                    }
                });

                if ($notificationContext !== null) {
                    try {
                        $event = $notificationContext['score_delta'] < 0
                            ? 'azure.security_score.decreased'
                            : 'azure.security_score.improved';
                        app(NotificationRuleService::class)->trigger($event, $notificationContext);
                    } catch (\Throwable $exception) {
                        report($exception);
                    }
                }
            }

            // Keep seven calendar days of score snapshots for the subscription.
            // This runs only after Azure has returned successfully for this subscription.
            $retentionStart = Carbon::now()->startOfDay()->subDays(6);
            $stats['pruned'] += AzureSecurityScore::query()
                ->where('subscription_id', $subscription)
                ->where('captured_at', '<', $retentionStart)
                ->delete();
        }

        return $stats;
    }

    /**
     * Retry only transient Azure gateway responses for the security score request.
     * Certificate verification remains enabled by the shared Azure HTTP client.
     */
    private function getWithGatewayRetry(string $url): Response
    {
        $retryableStatuses = [502, 503, 504];
        $maxAttempts = 4;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $response = $this->azure->get($url);

            if (! in_array($response->status(), $retryableStatuses, true) || $attempt === $maxAttempts) {
                return $response;
            }

            usleep($attempt * 500000);
        }

        throw new RuntimeException('Azure security score request exhausted its retry attempts.');
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
        ];
    }
}
