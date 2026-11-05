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

class SubscriptionMetricGraphChart extends ChartWidget
{
    public AzureSubscription $record;

    public int $graphId;

    public string $start;

    public string $end;

    public string $interval;

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected function getHeading(): ?string
    {
        return $this->graph()->title;
    }

    protected function getDescription(): ?string
    {
        $resource = $this->graph()->resource;

        return $resource?->name.' · '.$resource?->resource_type;
    }

    protected function getData(): array
    {
        abort_unless($this->canViewMetrics(), 403);

        $graph = $this->graph();
        $results = app(AzureMetricsService::class)->query(
            $graph->resource,
            $graph->series,
            $this->start,
            $this->end,
            $this->interval,
        );

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

                        $points[$timestamp] = (float) $value;
                        $labels[$timestamp] = Carbon::parse($timestamp)
                            ->timezone('Asia/Kolkata')
                            ->format('d M H:i');
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

        if ($labels === []) {
            return ['datasets' => [], 'labels' => []];
        }

        ksort($labels);
        $timestamps = array_keys($labels);

        foreach ($datasets as &$dataset) {
            $dataset['data'] = array_map(
                fn (string $timestamp): ?float => $dataset['data'][$timestamp] ?? null,
                $timestamps,
            );
        }
        unset($dataset);

        return [
            'datasets' => $datasets,
            'labels' => array_values($labels),
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
