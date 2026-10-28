<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Filament\Widgets\ChartWidget;

class SubscriptionCostHistoryChart extends ChartWidget
{
    public AzureSubscription $record;

    protected ?string $heading = 'Cost History';

    protected string $color = 'primary';

    protected ?string $pollingInterval = null;

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start')
            ->get(['period_start', 'amount', 'currency']);

        return [
            'datasets' => [
                [
                    'label' => 'Cost',
                    'data' => $costs
                        ->map(fn (AzureSubscriptionCost $cost): float => (float) $cost->amount)
                        ->values()
                        ->all(),
                ],
            ],
            'labels' => $costs
                ->map(fn (AzureSubscriptionCost $cost): string => $cost->period_start->format('M Y'))
                ->values()
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function mount(): void
    {
        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderByDesc('period_start')
            ->limit(2)
            ->get(['amount']);

        if ($costs->count() < 2) {
            return;
        }

        $current = (float) $costs->get(0)->amount;
        $previous = (float) $costs->get(1)->amount;

        // For costs, higher spending is bad (red) and lower spending is good (green).
        $this->color = $current > $previous
            ? 'danger'
            : ($current < $previous ? 'success' : 'primary');
    }
}
