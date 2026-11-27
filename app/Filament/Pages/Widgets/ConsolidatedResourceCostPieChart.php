<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Filament\Pages\Widgets\Concerns\HasConsolidatedCostScope;
use App\Models\BillingResource;
use Filament\Support\RawJs;
use Filament\Support\Facades\FilamentColor;
use Filament\Widgets\ChartWidget;
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

        $labels = $resources->pluck('resource_type')->map(fn ($type): string => (string) $type)->values()->all();
        $values = $resources->map(fn ($resource): float => (float) $resource->total_cost)->values()->all();

        $palette = FilamentColor::getColor('primary') ?? [];
        $shades = [300, 400, 500, 600, 700, 800, 900, 950];
        $colors = collect($shades)
            ->map(fn (int $shade): ?string => isset($palette[$shade]) ? (string) $palette[$shade] : null)
            ->filter()
            ->values();

        $backgroundColors = collect(range(0, max(0, count($labels) - 1)))
            ->map(fn (int $index): string => $colors->get($index % max(1, $colors->count()), 'oklch(0.55 0.15 250)'))
            ->all();

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Cost (EUR)',
                'data' => $values,
                'backgroundColor' => $backgroundColors,
                'borderColor' => 'var(--fi-color-gray-950)',
                'borderWidth' => 1,
                'resourceTypes' => $labels,
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
                    const resourceType = chart.data.datasets[element.datasetIndex].resourceTypes?.[element.index];
                    if (resourceType) {
                        Livewire.dispatch('consolidated-cost-resource-type-filter', { resourceTypes: [resourceType] });
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
        return 'pie';
    }
}
