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
                && is_array($item['supportedAggregationTypes'] ?? null))
                ->map(fn ($item) => [
                    'name' => $item['name']['value'],
                    'display_name' => $item['name']['localizedValue'] ?? $item['name']['value'],
                    'unit' => $item['unit'] ?? null,
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

            $cacheKey = 'azure-metrics-query:' . sha1(json_encode([$resource->resource_id, $metric, $aggregation, $start, $end, $interval]));
            $results[] = Cache::remember($cacheKey, now()->addSeconds(45), function () use ($resource, $metric, $aggregation, $start, $end, $interval): array {
                $url = 'https://management.azure.com' . $resource->resource_id . '/providers/Microsoft.Insights/metrics'
                    . '?api-version=' . self::API_VERSION
                    . '&metricnames=' . rawurlencode($metric)
                    . '&timespan=' . rawurlencode($start . '/' . $end)
                    . '&interval=' . rawurlencode($interval)
                    . '&aggregation=' . rawurlencode($aggregation);
                $response = $this->azure->get($url);
                if ($response->failed()) {
                    throw new RuntimeException('Azure Monitor metrics query failed.');
                }

                return ['metric' => $metric, 'aggregation' => $aggregation, 'value' => $response->json('value', [])];
            });
        }

        return $results;
    }

    public function eligibleResources(AzureSubscription $subscription): \Illuminate\Database\Eloquent\Collection
    {
        return $subscription->billingResources()
            ->whereNotNull('resource_type')
            ->orderBy('name')
            ->get(['resource_id', 'subscription_id', 'name', 'resource_type', 'region', 'sku']);
    }
}
