<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BillingResource;
use App\Models\AzureSubscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AzureMetricsService
{
    private const API_VERSION = '2018-01-01';

    /**
     * Resource types for which this dashboard explicitly retrieves Azure
     * platform metrics. Keep this allowlist narrow: inventory resources such
     * as alerts, dashboards, network groups, and policy objects are not metric
     * targets and must never be sent to metricDefinitions.
     */
    private const SUPPORTED_RESOURCE_TYPES = [
        'microsoft.compute/virtualmachines',
        'microsoft.compute/virtualmachinescalesets',
        'microsoft.sql/servers',
        'microsoft.sql/servers/databases',
        'microsoft.sql/managedinstances',
        'microsoft.sql/managedinstances/databases',
        'microsoft.cache/redis',
        'microsoft.cache/redisenterprise',
        'microsoft.cache/redisenterprise/databases',
        'microsoft.web/sites',
        'microsoft.web/serverfarms',
        'microsoft.dbformysql/servers',
        'microsoft.dbformysql/flexibleservers',
        'microsoft.dbforpostgresql/servers',
        'microsoft.dbforpostgresql/flexibleservers',
        'microsoft.dbformariadb/servers',
        'microsoft.documentdb/databaseaccounts',
    ];

    public function __construct(private readonly AzureSubscriptionSyncService $azure) {}

    public function definitions(BillingResource $resource, bool $refresh = false): array
    {
        if (! $this->supportsMetrics($resource)) {
            return [];
        }

        $key = 'azure-metric-definitions:' . sha1(strtolower($resource->resource_id));
        if ($refresh) Cache::forget($key);

        return Cache::remember($key, now()->addMinutes(15), function () use ($resource): array {
            $resourceType = strtolower(trim((string) $resource->resource_type));
            $region = strtolower(trim((string) ($resource->region ?: 'global')));
            // Version the cache key because earlier builds incorrectly stored
            // the first region's namespace list under every resource region.
            $supportedTypesKey = 'azure-supported-metric-resource-types:v2:' . $region;
            $supportedTypes = Cache::get($supportedTypesKey);

            // Azure returns the supported platform metric namespaces with this
            // specific 400 response. Reuse that list to avoid requesting
            // definitions for resource types that Azure does not support.
            if (is_array($supportedTypes) && ! in_array($resourceType, $supportedTypes, true)) {
                return [];
            }

            $url = 'https://management.azure.com' . $resource->resource_id . '/providers/Microsoft.Insights/metricDefinitions?api-version=' . self::API_VERSION;
            $response = $this->azure->get($url);
            if ($response->failed()) {
                $payload = $response->json();
                $azureError = is_array($payload) ? ($payload['error'] ?? $payload) : [];
                $azureCode = is_array($azureError) ? ($azureError['code'] ?? null) : null;
                $azureMessage = is_array($azureError)
                    ? ($azureError['message'] ?? $azureError['Message'] ?? null)
                    : null;

                if (! is_string($azureMessage) || $azureMessage === '') {
                    $azureMessage = trim($response->body());
                }

                if ($response->status() === 400 && is_string($azureMessage)) {
                    // Azure can include separate supported-namespace lists for
                    // multiple regions in one error message. Never associate
                    // the first list with the resource's region by assumption.
                    preg_match_all(
                        '/supported ones are (.+?), Region:\\s*([^,\\)]+)(?:, TraceId:|\\))/is',
                        $azureMessage,
                        $regionMatches,
                        PREG_SET_ORDER,
                    );

                    foreach ($regionMatches as $regionMatch) {
                        $listedRegion = strtolower(trim($regionMatch[2] ?? ''));
                        if ($listedRegion !== $region) {
                            continue;
                        }

                        $supportedTypes = collect(explode(',', $regionMatch[1]))
                            ->map(fn (string $type): string => strtolower(trim($type)))
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        if ($supportedTypes !== []) {
                            Cache::put($supportedTypesKey, $supportedTypes, now()->addHours(24));

                            if (! in_array($resourceType, $supportedTypes, true)) {
                                return [];
                            }
                        }

                        break;
                    }
                }

                throw new RuntimeException(sprintf(
                    'Azure Monitor metric definitions request failed (HTTP %d%s%s)',
                    $response->status(),
                    is_string($azureCode) && $azureCode !== '' ? ', '.$azureCode : '',
                    is_string($azureMessage) && $azureMessage !== '' ? ': '.$azureMessage : '',
                ));
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

            $availableIntervals = $definition['time_grain'] ?? [];
            if ($availableIntervals !== [] && ! in_array($interval, $availableIntervals, true)) {
                throw new RuntimeException(sprintf(
                    'Metric "%s" does not support the dashboard interval %s. Supported intervals: %s.',
                    $metric,
                    $interval,
                    implode(', ', $availableIntervals),
                ));
            }

            $metricInterval = $interval;
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
                    $payload = $response->json();
                    $azureError = is_array($payload) ? ($payload['error'] ?? $payload) : [];
                    $azureCode = is_array($azureError) && is_string($azureError['code'] ?? null)
                        ? $azureError['code']
                        : null;
                    $azureMessage = is_array($azureError) && is_string($azureError['message'] ?? null)
                        ? $azureError['message']
                        : null;
                    $responseBody = trim($response->body());

                    Log::error('Azure Monitor metrics query rejected.', [
                        'http_status' => $response->status(),
                        'azure_error_code' => $azureCode,
                        'azure_error_message' => $azureMessage,
                        'response_body' => mb_substr($responseBody, 0, 4000),
                        'resource_id' => $resource->resource_id,
                        'resource_type' => $resource->resource_type,
                        'metric' => $metric,
                        'aggregation' => $aggregation,
                        'namespace' => $definition['namespace'] ?? null,
                        'timespan_start' => $start,
                        'timespan_end' => $end,
                        'interval' => $metricInterval,
                    ]);

                    throw new RuntimeException(sprintf(
                        'Azure Monitor metrics query failed (HTTP %d%s%s).',
                        $response->status(),
                        $azureCode ? ', '.$azureCode : '',
                        $azureMessage ? ': '.$azureMessage : '',
                    ));
                }

                $values = $response->json('value', []);
                $returned = collect(is_array($values) ? $values : [])->map(function (array $metricResult): array {
                    $points = collect($metricResult['timeseries'] ?? [])
                        ->flatMap(fn (array $line) => $line['data'] ?? [])
                        ->pluck('timeStamp')
                        ->filter(fn ($timestamp) => is_string($timestamp))
                        ->sort()
                        ->values();

                    return [
                        'timespan' => $metricResult['timespan'] ?? null,
                        'interval' => $metricResult['interval'] ?? null,
                        'point_count' => $points->count(),
                        'first_point' => $points->first(),
                        'last_point' => $points->last(),
                    ];
                })->all();

                Log::debug('Azure Monitor metrics response received.', [
                    'resource_id' => $resource->resource_id,
                    'metric' => $metric,
                    'aggregation' => $aggregation,
                    'requested_timespan_start' => $start,
                    'requested_timespan_end' => $end,
                    'requested_interval' => $metricInterval,
                    'azure_metric_results' => $returned,
                ]);

                return [
                    'metric' => $metric,
                    'aggregation' => $aggregation,
                    'interval' => $metricInterval,
                    'value' => is_array($values) ? $values : [],
                    'response_metadata' => $returned,
                ];
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
            ->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(resource_type)'), self::SUPPORTED_RESOURCE_TYPES)
            ->orderBy('name')
            ->get(['resource_id', 'subscription_id', 'name', 'resource_type', 'region', 'sku']);
    }

    public function supportsMetrics(BillingResource $resource): bool
    {
        return in_array(
            strtolower(trim((string) $resource->resource_type)),
            self::SUPPORTED_RESOURCE_TYPES,
            true,
        );
    }
}
