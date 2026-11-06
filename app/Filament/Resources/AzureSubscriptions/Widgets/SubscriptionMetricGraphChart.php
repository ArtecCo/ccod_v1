<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Models\Developer;
use App\Models\SubscriptionMetricGraph;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\AzureMetricsService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubscriptionMetricGraphChart extends ChartWidget
{
    public AzureSubscription $record;

    public int $graphId;

    public string $start;

    public string $end;

    public string $interval;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        abort_unless($this->canViewMetrics(), 403);

        $graph = $this->graph();
        try {
            $results = app(AzureMetricsService::class)->query(
                $graph->resource,
                $graph->series,
                $this->start,
                $this->end,
                $this->interval,
            );
        } catch (Throwable $exception) {
            Log::warning('Metric graph could not load Azure data; rendering an empty chart instead.', [
                'subscription_id' => $this->record->subscription_id,
                'graph_id' => $graph->id,
                'resource_id' => $graph->resource?->resource_id,
                'exception' => $exception->getMessage(),
            ]);

            return ['datasets' => [], 'labels' => []];
        }

        $labels = [];
        $datasets = [];

        foreach ($results as $result) {
            $aggregation = strtolower($result['aggregation']);

            foreach ($result['value'] ?? [] as $metric) {
                foreach ($metric['timeseries'] ?? [] as $line) {
                    $points = [];

                    foreach ($line['data'] ?? [] as $point) {
                        $timestamp = $point['timeStamp'] ?? null;
                        $value = $point[$aggregation] ?? null;

                        if (! is_string($timestamp) || ! is_numeric($value)) {
                            continue;
                        }

                        $minute = Carbon::parse($timestamp)->utc()->startOfMinute()->format('Y-m-d\\TH:i:s\\Z');
                        $points[$minute] = (float) $value;
                    }

                    if ($points === []) {
                        continue;
                    }

                    $dimensionLabels = collect($line['metadatavalues'] ?? [])
                        ->map(fn (array $dimension): string => ($dimension['name']['localizedValue'] ?? $dimension['name']['value'] ?? 'Dimension')
                            .': '.($dimension['value'] ?? ''))
                        ->filter()
                        ->implode(', ');

                    $datasets[] = [
                        'label' => $result['metric'].($dimensionLabels !== '' ? ' · '.$dimensionLabels : ''),
                        'data' => $points,
                        'spanGaps' => false,
                        'tension' => 0.25,
                    ];
                }
            }
        }

        if ($datasets === []) {
            return ['datasets' => [], 'labels' => []];
        }

        // Build the complete requested one-minute timeline. Azure can return
        // only a subset of the requested window; retaining the full timeline
        // makes that missing history visible instead of shrinking the X-axis.
        $cursor = Carbon::parse($this->start)->utc()->startOfMinute();
        $lastMinute = Carbon::parse($this->end)->utc()->startOfMinute();
        $timestamps = [];

        while ($cursor->lessThanOrEqualTo($lastMinute)) {
            $timestamps[] = $cursor->format('Y-m-d\\TH:i:s\\Z');
            $cursor->addMinute();
        }

        foreach ($datasets as &$dataset) {
            $dataset['data'] = array_map(
                fn (string $timestamp): ?float => $dataset['data'][$timestamp] ?? null,
                $timestamps,
            );
        }
        unset($dataset);

        return [
            'datasets' => $datasets,
            'labels' => array_map(
                fn (string $timestamp): string => Carbon::parse($timestamp)
                    ->timezone('Asia/Kolkata')
                    ->format('d M H:i'),
                $timestamps,
            ),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function graph(): SubscriptionMetricGraph
    {
        return SubscriptionMetricGraph::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->with('resource')
            ->findOrFail($this->graphId);
    }

    private function canViewMetrics(): bool
    {
        $developer = auth()->guard('developers')->user();

        if ($developer instanceof Developer && $developer->is_active) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User
            && $user->is_active
            && app(AccessAuthorizationService::class)->hasPermission($user, 'resources.metrics.view', $this->record);
    }
}
