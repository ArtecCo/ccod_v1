<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Filament\Pages\Widgets\Concerns\HasConsolidatedCostScope;
use App\Models\BillingResource;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\Url;

class ConsolidatedResourceCostPieChart extends ChartWidget
{
    use HasConsolidatedCostScope;

    #[Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    protected ?string $heading = 'Cost Distribution by Resource';

    protected ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $subscriptions = $this->authorizedCostSubscriptions($this->scopeFilter);
        $subscriptionIds = $subscriptions->pluck('subscription_id')->all();

        $resources = BillingResource::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->whereNotNull('cost_eur')
            ->select(['resource_id', 'name'])
            ->selectRaw('SUM(cost_eur) AS total_cost')
            ->groupBy('resource_id', 'name')
            ->orderByDesc('total_cost')
            ->get();

        if ($resources->isEmpty()) {
            return ['labels' => [], 'datasets' => []];
        }

        $visible = $resources->count() > 10 ? $resources->take(9) : $resources;
        $labels = $visible->map(fn (BillingResource $resource): string => $resource->name ?: $resource->resource_id)->values()->all();
        $values = $visible->map(fn (BillingResource $resource): float => (float) $resource->total_cost)->values()->all();
        $resourceIds = $visible->map(fn (BillingResource $resource): array => [(string) $resource->resource_id])->values()->all();

        if ($resources->count() > 10) {
            $otherResources = $resources->skip(9);
            $labels[] = 'Other resources';
            $values[] = (float) $otherResources->sum(fn (BillingResource $resource): float => (float) $resource->total_cost);
            $resourceIds[] = $otherResources->pluck('resource_id')->map(fn ($id): string => (string) $id)->values()->all();
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Cost (EUR)',
                'data' => $values,
                'resourceIds' => $resourceIds,
            ]],
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                responsive: true,
                maintainAspectRatio: false,
                onClick: (event, elements, chart) => {
                    if (! elements.length) return;
                    const element = elements[0];
                    const resourceIds = chart.data.datasets[element.datasetIndex].resourceIds?.[element.index] ?? [];
                    if (resourceIds.length) {
                        Livewire.dispatch('consolidated-cost-resource-filter', { resourceIds });
                    }
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
