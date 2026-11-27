<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Filament\Pages\Widgets\Concerns\HasConsolidatedCostScope;
use App\Models\BillingResource;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class ConsolidatedResourceCostPieChart extends ChartWidget
{
    use HasConsolidatedCostScope;

    #[Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    public ?string $selectedSubscriptionId = null;

    protected ?string $heading = 'Cost Distribution by Resource Type';

    protected ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $subscriptions = $this->authorizedCostSubscriptions($this->scopeFilter);
        $subscriptionIds = $subscriptions->pluck('subscription_id')->all();

        $query = BillingResource::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->whereNotNull('cost_eur');

        if ($this->selectedSubscriptionId !== null && in_array($this->selectedSubscriptionId, $subscriptionIds, true)) {
            $query->where('subscription_id', $this->selectedSubscriptionId);
        }

        $resources = $query
            ->selectRaw("COALESCE(NULLIF(resource_type, ''), 'Unknown') AS resource_type")
            ->selectRaw('SUM(cost_eur) AS total_cost')
            ->groupBy('resource_type')
            ->orderByDesc('total_cost')
            ->get();

        if ($resources->isEmpty()) {
            return ['labels' => [], 'datasets' => []];
        }

        $topTypes = $resources->take(5);
        $labels = $topTypes
            ->pluck('resource_type')
            ->map(fn ($type): string => $this->formatResourceTypeLabel((string) $type))
            ->values()
            ->all();
        $values = $topTypes
            ->map(fn ($resource): float => (float) $resource->total_cost)
            ->values()
            ->all();
        $resourceTypes = $topTypes
            ->pluck('resource_type')
            ->map(fn ($type): array => [(string) $type])
            ->values()
            ->all();

        if ($resources->count() > 5) {
            $otherTypes = $resources->skip(5);
            $labels[] = 'Other';
            $values[] = (float) $otherTypes->sum(fn ($resource): float => (float) $resource->total_cost);
            $resourceTypes[] = $otherTypes
                ->pluck('resource_type')
                ->map(fn ($type): string => (string) $type)
                ->values()
                ->all();
        }

        // Use a restrained range of the configured primary palette rather than
        // cycling through bright, high-contrast colours.
        $palette = FilamentColor::getColor('primary') ?? [];
        $shades = [300, 400, 500, 600, 700, 800];
        $colors = collect($shades)
            ->map(fn (int $shade): ?string => isset($palette[$shade]) ? (string) $palette[$shade] : null)
            ->filter()
            ->values();

        $backgroundColors = collect($labels)
            ->keys()
            ->map(fn (int $index): string => $colors->get($index, (string) ($palette[500] ?? '#9CA3AF')))
            ->all();

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Cost (EUR)',
                'data' => $values,
                'backgroundColor' => $backgroundColors,
                'borderWidth' => 0,
                'hoverBorderWidth' => 0,
                'hoverOffset' => 5,
                'resourceTypes' => $resourceTypes,
            ]],
        ];
    }

    protected function formatResourceTypeLabel(string $resourceType): string
    {
        $resourceName = Str::afterLast($resourceType, '/');

        if ($resourceName === $resourceType && str_contains($resourceType, '.')) {
            $resourceName = Str::afterLast($resourceType, '.');
        }

        return Str::headline(Str::snake($resourceName));
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '58%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 12,
                        },
                    },
                    tooltip: {
                        callbacks: {
                            title: (items) => {
                                if (! items.length) return '';
                                const dataset = items[0].chart.data.datasets[items[0].datasetIndex];
                                const types = dataset.resourceTypes?.[items[0].dataIndex] ?? [];
                                return types.length === 1 ? types[0] : 'Other resource types (' + types.length + ')';
                            },
                            label: (item) => '€ ' + Number(item.raw).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                        },
                    },
                },
                onClick: (event, elements, chart) => {
                    if (! elements.length) return;
                    const element = elements[0];
                    const resourceTypes = chart.data.datasets[element.datasetIndex].resourceTypes?.[element.index] ?? [];
                    if (resourceTypes.length) {
                        Livewire.dispatch('consolidated-cost-resource-type-filter', { resourceTypes });
                    }
                },
            }
        JS);
    }

    #[On('consolidated-cost-subscription-filter')]
    public function applySubscriptionFilter(string $subscriptionId): void
    {
        $authorizedIds = $this->authorizedCostSubscriptions($this->scopeFilter)
            ->pluck('subscription_id')->map(fn ($id): string => (string) $id)->all();

        if (! in_array($subscriptionId, $authorizedIds, true)) {
            return;
        }

        $this->selectedSubscriptionId = $this->selectedSubscriptionId === $subscriptionId ? null : $subscriptionId;
        $this->cachedData = null;
        $this->updateChartData();
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
