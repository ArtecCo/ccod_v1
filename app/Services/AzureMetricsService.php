<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BillingResource;
use App\Models\AzureSubscription;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class AzureMetricsService
{
    private const API_VERSION = '2018-01-01';

    public function __construct(private readonly AzureSubscriptionSyncService $azure) {}

    public function definitions(BillingResource $resource, bool $refresh = false): array
    {
        $key = 'azure-metric-definitions:' . sha1(strtolower($resource->resource_id));
        if ($refresh) Cache::forget($key);

        return Cache::remember($key, now()->addMinutes(15), function () use ($resource): array {
            $url = 'https://management.azure.com' . $resource->resource_id . '/providers/Microsoft.Insights/metricDefinitions?api-version=' . self::API_VERSION;
            $response = $this->azure->get($url);
            if ($response->failed()) {
                throw new RuntimeException('Azure Monitor could not retrieve metric definitions for this resource.');
            }

            $items = $response->json('value', []);
            if (! is_array($items)) return [];

            return collect($items)->filter(fn ($item) => is_array($item)
                && is_string($item['name']['value'] ?? null)
                && is_array($item['supportedAggregationTypes'] ?? null)
                && $item['supportedAggregationTypes'] !== [])
                ->map(fn ($item) => [
                    'name' => $item['name']['value'],
                    'display_name' => $item['name']['localizedValue'] ?? $item['name']['value'],
                    'unit' => $item['unit'] ?? null,
                    'namespace' => $item['namespace'] ?? null,
                    'primary_aggregation' => $item['primaryAggregationType'] ?? null,
                    'aggregations' => array_values(array_filter($item['supportedAggregationTypes'], 'is_string')),
                    'time_grain' => collect($item['metricAvailabilities'] ?? [])
                        ->pluck('timeGrain')->filter()->unique()->values()->all(),
                    'dimensions' => collect($item['dimensions'] ?? [])
                        ->pluck('value')->filter()->values()->all(),
                ])->values()->all();
        });
    }

    public function query(BillingResource $resource, array $series, string $start, string $end, string $interval): array
    {
        $definitions = collect($this->definitions($resource))->keyBy('name');
        $results = [];

        foreach ($series as $item) {
            $metric = $item['metric'] ?? null;
            $aggregation = $item['aggregation'] ?? null;
            $definition = $definitions->get($metric);

            if (! $definition || ! is_string($aggregation) || ! in_array($aggregation, $definition['aggregations'], true)) {
                throw new RuntimeException('One or more selected metrics or aggregations are not supported by this resource.');
            }

            $metricInterval = $this->resolveInterval($definition['time_grain'] ?? [], $interval);
            $cacheKey = 'azure-metrics-query:' . sha1(json_encode([$resource->resource_id, $definition['namespace'] ?? null, $metric, $aggregation, $start, $end, $metricInterval]));
            $results[] = Cache::remember($cacheKey, now()->addSeconds(20), function () use ($resource, $metric, $aggregation, $start, $end, $metricInterval, $definition): array {
                $url = 'https://management.azure.com' . $resource->resource_id . '/providers/Microsoft.Insights/metrics'
                    . '?api-version=' . self::API_VERSION
                    . '&metricnames=' . rawurlencode($metric)
                    . '&timespan=' . rawurlencode($start . '/' . $end)
                    . '&interval=' . rawurlencode($metricInterval)
                    . '&aggregation=' . rawurlencode($aggregation)
                    . (is_string($definition['namespace'] ?? null) && $definition['namespace'] !== '' ? '&metricnamespace=' . rawurlencode($definition['namespace']) : '');
                $response = $this->azure->get($url);
                if ($response->failed()) {
                    throw new RuntimeException('Azure Monitor metrics query failed.');
                }

                return ['metric' => $metric, 'aggregation' => $aggregation, 'interval' => $metricInterval, 'value' => $response->json('value', [])];
            });
        }

        return $results;
    }

    private function resolveInterval(array $available, string $requested): string
    {
        $minutes = static function (string $value): ?int {
            if (preg_match('/^PT(\\d+)M$/', $value, $matches) === 1) return (int) $matches[1];
            if (preg_match('/^PT(\\d+)H$/', $value, $matches) === 1) return (int) $matches[1] * 60;
            if (preg_match('/^P(\\d+)D$/', $value, $matches) === 1) return (int) $matches[1] * 1440;
            return null;
        };

        $requestedMinutes = $minutes($requested);
        $choices = collect($available)
            ->filter(fn ($value): bool => is_string($value) && $minutes($value) !== null)
            ->sortBy(fn (string $value): int => $minutes($value))
            ->values();

        if ($choices->isEmpty() || $requestedMinutes === null || $choices->contains($requested)) return $requested;

        return $choices->first(fn (string $value): bool => $minutes($value) >= $requestedMinutes)
            ?? $choices->last();
    }

    public function eligibleResources(AzureSubscription $subscription): \Illuminate\Database\Eloquent\Collection
    {
        return $subscription->billingResources()
            ->whereNotNull('resource_type')
            ->orderBy('name')
            ->get(['resource_id', 'subscription_id', 'name', 'resource_type', 'region', 'sku']);
    }
}
