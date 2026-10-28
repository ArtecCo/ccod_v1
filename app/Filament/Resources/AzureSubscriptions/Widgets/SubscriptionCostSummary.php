<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionCostSummary extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start')
            ->get(['period_start', 'amount', 'currency']);

        $current = $costs->last();
        $previous = $costs->count() > 1 ? $costs->get($costs->count() - 2) : null;
        $chart = $costs
            ->map(fn (AzureSubscriptionCost $cost): float => (float) $cost->amount)
            ->values()
            ->all();

        $currency = $current?->currency ?: 'EUR';
        $currentAmount = $current !== null ? (float) $current->amount : null;
        $previousAmount = $previous !== null ? (float) $previous->amount : null;
        $difference = $this->percentageDifference($currentAmount, $previousAmount);

        $actual = Stat::make(
            'Current Month',
            $this->formatMoney($currentAmount, $currency),
        )
            ->icon('heroicon-o-banknotes')
            ->color('gray');

        if (count($chart) >= 2) {
            $actual = $actual->chart($chart)->chartColor('gray');
        }

        if ($difference !== null) {
            $actual = $actual
                ->description($this->formatDifference($difference) . ' vs previous month')
                ->descriptionIcon($difference >= 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down');
        }

        return [
            $actual,
            Stat::make('12-Month Total', $this->formatMoney(
                $costs->sum(fn (AzureSubscriptionCost $cost): float => (float) $cost->amount),
                $currency,
            ))
                ->icon('heroicon-o-calendar-days')
                ->color('gray'),
            Stat::make('Forecasted Cost', $this->formatMoney(
                $this->record->costForecast?->forecast_amount,
                'EUR',
            ))
                ->icon('heroicon-o-chart-bar')
                ->color('gray'),
            Stat::make('Resource Cost', $this->formatMoney(
                $this->record->billingResources()->sum('cost_eur'),
                'EUR',
            ))
                ->icon('heroicon-o-cube')
                ->color('gray'),
        ];
    }

    private function percentageDifference(?float $current, ?float $previous): ?float
    {
        if ($current === null || $previous === null || $previous == 0.0) {
            return null;
        }

        return (($current - $previous) / abs($previous)) * 100;
    }

    private function formatDifference(float $difference): string
    {
        return ($difference >= 0 ? '+' : '') . number_format($difference, 1) . '%';
    }

    private function formatMoney(mixed $value, string $currency): string
    {
        if ($value === null) {
            return '—';
        }

        return strtoupper($currency) . ' ' . number_format((float) $value, 2);
    }
}
