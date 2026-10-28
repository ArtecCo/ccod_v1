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
        $differenceAmount = $this->differenceAmount($currentAmount, $previousAmount);
        $differencePercentage = $this->percentageDifference($currentAmount, $previousAmount);
        $periodLabel = $current?->period_start?->format('M Y') ?: now()->format('M Y');

        // Cost trend semantics are intentionally inverted: increasing spend is bad,
        // decreasing spend is good. This is limited to the Cost tab widgets.
        $trendColor = $differencePercentage === null
            ? 'primary'
            : ($differencePercentage > 0 ? 'danger' : ($differencePercentage < 0 ? 'success' : 'primary'));

        $mtd = Stat::make('MTD Cost', $this->formatMoney($currentAmount, $currency))
            ->icon('heroicon-o-banknotes')
            ->color($trendColor);

        if (count($sparkline) >= 2) {
            $mtd = $mtd->chart($sparkline)->chartColor($trendColor);
        }

        if ($differencePercentage !== null) {
            $mtd = $mtd
                ->description($this->formatDifference($differencePercentage) . ' vs previous month')
                ->descriptionIcon($differencePercentage > 0
                    ? 'heroicon-m-arrow-trending-up'
                    : ($differencePercentage < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                ->descriptionColor($trendColor);
        }

        $forecast = $this->record->costForecast;
        $forecastRemaining = $forecast?->forecast_amount !== null
            ? (float) $forecast->forecast_amount
            : null;
        $forecastTotal = $currentAmount !== null && $forecastRemaining !== null
            ? $currentAmount + $forecastRemaining
            : null;

        return [
            $mtd,
            Stat::make('Budget', $this->formatMoney($this->record->budgets()->sum('amount'), $this->budgetCurrency()))
                ->description('For ' . $periodLabel)
                ->icon('heroicon-o-wallet')
                ->color('primary'),
            Stat::make('Forecast', $this->formatMoney($forecastTotal, $forecast?->currency ?: $currency))
                ->description('For ' . $periodLabel)
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),
            Stat::make('MoM Difference', $this->formatSignedMoney($differenceAmount, $currency))
                ->description($differencePercentage === null
                    ? 'Previous month unavailable'
                    : $this->formatDifference($differencePercentage) . ' vs previous month')
                ->descriptionIcon($differencePercentage === null
                    ? 'heroicon-m-minus'
                    : ($differencePercentage > 0 ? 'heroicon-m-arrow-trending-up' : ($differencePercentage < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus')))
                ->descriptionColor($trendColor)
                ->color($trendColor),
            Stat::make('Last Synced', $this->record->last_synced?->format('d M Y, H:i') ?? '—')
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
        ];
    }

    private function differenceAmount(?float $current, ?float $previous): ?float
    {
        if ($current === null || $previous === null) {
            return null;
        }

        return $current - $previous;
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

    private function formatSignedMoney(?float $value, string $currency): string
    {
        if ($value === null) {
            return '—';
        }

        $sign = $value > 0 ? '+' : ($value < 0 ? '-' : '');

        return $sign . strtoupper($currency) . ' ' . number_format(abs($value), 2);
    }
}
