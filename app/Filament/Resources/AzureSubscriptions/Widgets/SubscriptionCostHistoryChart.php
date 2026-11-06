<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;


use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class SubscriptionCostHistoryChart extends ChartWidget
{
    public AzureSubscription $record;

    /**
     * The chart and its trend colour use the same cost history read.
     *
     * @var array<int, array{label: string, amount: float}>
     */
    public array $costHistory = [];

    protected ?string $heading = 'Cost Trend';

    protected string $color = 'primary';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    public function mount(): void
    {
        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start')
            ->get(['period_start', 'amount']);

        $this->costHistory = $costs
            ->map(fn (AzureSubscriptionCost $cost): array => [
                'label' => $cost->period_start->format('M Y'),
                'amount' => (float) $cost->amount,
            ])
            ->values()
            ->all();

        if (count($this->costHistory) < 2) {
            return;
        }

        $current = $this->costHistory[count($this->costHistory) - 1]['amount'];
        $previous = $this->costHistory[count($this->costHistory) - 2]['amount'];

        // For costs, higher spending is bad (red) and lower spending is good (green).
        $this->color = $current > $previous
            ? 'danger'
            : ($current < $previous ? 'success' : 'primary');
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Cost',
                    'data' => array_column($this->costHistory, 'amount'),
                ],
            ],
            'labels' => array_column($this->costHistory, 'label'),
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => '€' + Number(value).toLocaleString(),
                        },
                    },
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
