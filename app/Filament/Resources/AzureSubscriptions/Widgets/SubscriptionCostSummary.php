<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionCostSummary extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 5;

    protected function getStats(): array
    {
        $costs = AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start')
            ->get(['period_start', 'amount', 'currency']);

        $current = $costs->last();
        $previous = $costs->count() > 1 ? $costs->get($costs->count() - 2) : null;
        $sparkline = $costs
            ->map(fn (AzureSubscriptionCost $cost): float => (float) $cost->amount)
            ->values()
            ->all();

        $currency = $current?->currency ?: 'EUR';
        $currentAmount = $current !== null ? (float) $current->amount : null;
        $previousAmount = $previous !== null ? (float) $previous->amount : null;
        $difference = $this->percentageDifference($currentAmount, $previousAmount);

        // Cost trend semantics are intentionally inverted: increasing spend is bad,
        // decreasing spend is good. This is limited to the Cost tab widgets.
        $trendColor = $difference === null
            ? 'primary'
            : ($difference > 0 ? 'danger' : ($difference < 0 ? 'success' : 'primary'));

        $mtd = Stat::make('MTD Cost', $this->formatMoney($currentAmount, $currency))
            ->icon('heroicon-o-banknotes')
            ->color($trendColor);

        if (count($sparkline) >= 2) {
            $mtd = $mtd->chart($sparkline)->chartColor($trendColor);
        }

        if ($difference !== null) {
            $mtd = $mtd
                ->description($this->formatDifference($difference) . ' vs previous month')
                ->descriptionIcon($difference > 0
                    ? 'heroicon-m-arrow-trending-up'
                    : ($difference < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->descriptionColor($trendColor);
        }

        $forecast = $this->record->costForecast;

        return [
            $mtd,
            Stat::make('Budget', $this->formatMoney($this->record->budgets()->sum('amount'), $this->budgetCurrency()))
                ->icon('heroicon-o-wallet')
                ->color('primary'),
            Stat::make('Budget Actual', $this->formatMoney($this->record->budgets()->sum('current_spend'), $this->budgetCurrency()))
                ->icon('heroicon-o-banknotes')
                ->color('primary'),
            Stat::make('Forecast', $this->formatMoney($forecast?->forecast_amount, $forecast?->currency ?: 'EUR'))
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
            Stat::make('MoM Difference', $difference === null ? '—' : $this->formatDifference($difference))
                ->description($difference === null ? 'Previous month unavailable' : 'vs previous month')
                ->descriptionIcon($difference === null
                    ? 'heroicon-m-minus'
                    : ($difference > 0 ? 'heroicon-m-arrow-trending-up' : ($difference < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus')))
                ->descriptionColor($trendColor)
                ->color($trendColor),
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

    private function budgetCurrency(): string
    {
        return $this->record->budgets()->value('currency') ?: 'EUR';
    }

    private function formatMoney(mixed $value, string $currency): string
    {
        if ($value === null) {
            return '—';
        }

        return strtoupper($currency) . ' ' . number_format((float) $value, 2);
    }
}
