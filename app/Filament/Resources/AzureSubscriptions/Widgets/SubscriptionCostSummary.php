<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionCostSummary extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $budget = $this->record->budgets()->sum('amount');
        $budgetActual = $this->record->budgets()->sum('current_spend');
        $budgetForecast = $this->record->budgets()->sum('forecast_spend');
        $forecast = $this->record->costForecast?->forecast_amount;
        $resourceCost = $this->record->billingResources()->sum('cost_eur');

        return [
            Stat::make('Actual Cost', $this->formatEur($this->record->mtd_spend_eur)),
            Stat::make('Forecasted Cost', $this->formatEur($forecast)),
            Stat::make('Budget', $this->formatEur($budget)),
            Stat::make('Budget Actual', $this->formatEur($budgetActual)),
            Stat::make('Budget Forecast', $this->formatEur($budgetForecast)),
            Stat::make('Resource Cost', $this->formatEur($resourceCost)),
        ];
    }

    private function formatEur(mixed $value): string
    {
        return $value === null ? '—' : '€' . number_format((float) $value, 2);
    }
}
