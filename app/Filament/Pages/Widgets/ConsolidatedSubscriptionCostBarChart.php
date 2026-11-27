<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Filament\Pages\Widgets\Concerns\HasConsolidatedCostScope;
use App\Models\BillingResource;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ConsolidatedSubscriptionCostBarChart extends ChartWidget
{
    use HasConsolidatedCostScope;

    #[\Livewire\Attributes\Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    protected ?string $heading = 'Cost Distribution by Subscription';

    protected ?string $maxHeight = '320px';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $subscriptions = $this->authorizedCostSubscriptions($this->scopeFilter);
        $subscriptionIds = $subscriptions->pluck('subscription_id')->all();

        $totals = BillingResource::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->whereNotNull('cost_eur')
            ->select('subscription_id')
            ->selectRaw('SUM(cost_eur) AS total_cost')
            ->groupBy('subscription_id')
            ->orderByDesc('total_cost')
            ->get()
            ->keyBy('subscription_id');

        $rows = $subscriptions
            ->filter(fn ($subscription): bool => $totals->has($subscription->subscription_id))
            ->sortByDesc(fn ($subscription): float => (float) $totals->get($subscription->subscription_id)->total_cost)
            ->values();

        return [
            'labels' => $rows->map(fn ($subscription): string => (string) $subscription->display_name)->all(),
            'datasets' => [[
                'label' => 'Cost (EUR)',
                'data' => $rows->map(fn ($subscription): float => (float) $totals->get($subscription->subscription_id)->total_cost)->all(),
                'subscriptionIds' => $rows->map(fn ($subscription): string => (string) $subscription->subscription_id)->all(),
            ]],
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                onClick: (event, elements, chart) => {
                    if (! elements.length) return;
                    const element = elements[0];
                    const subscriptionId = chart.data.datasets[element.datasetIndex].subscriptionIds?.[element.index];
                    if (subscriptionId) {
                        Livewire.dispatch('consolidated-cost-subscription-filter', { subscriptionId });
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            callback: (value) => '€ ' + Number(value).toLocaleString(),
                        },
                    },
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
