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

    protected string $color = 'gray';

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
}
