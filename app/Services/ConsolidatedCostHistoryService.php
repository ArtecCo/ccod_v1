<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureBudget;
use App\Models\AzureCostForecast;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\ConsolidatedCostHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ConsolidatedCostHistoryService
{
    /**
     * Persist monthly totals for exactly the subscription set authorized for
     * the current user and selected dashboard scope.
     *
     * Historical actuals are backfilled from Azure's monthly cost records.
     * Budget and forecast are captured for the current month as they are
     * point-in-time values from the latest Azure synchronization.
     */
    public function capture(Collection $subscriptions, string $scopeFilter): string
    {
        $ids = $subscriptions->pluck('subscription_id')->filter()->unique()->sort()->values()->all();
        $scopeHash = hash('sha256', $scopeFilter . '|' . implode('|', $ids));

        if ($ids === []) {
            return $scopeHash;
        }

        DB::transaction(function () use ($ids, $scopeHash, $scopeFilter): void {
            $monthlyCosts = AzureSubscriptionCost::query()
                ->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')
                ->orderBy('period_start')
                ->get(['period_start', 'amount', 'currency'])
                ->groupBy(fn (AzureSubscriptionCost $cost): string => $cost->period_start->toDateString());

            $currencySet = $monthlyCosts->flatten(1)->pluck('currency')->filter()->unique()->values();
            // Never add amounts denominated in different currencies.
            $currency = $currencySet->count() === 1 ? $currencySet->first() : null;
            $currentPeriod = now()->startOfMonth()->toDateString();

            foreach ($monthlyCosts as $period => $rows) {
                $periodCurrencies = $rows->pluck('currency')->filter()->unique();
                $periodCurrency = $periodCurrencies->count() === 1 ? $periodCurrencies->first() : null;
                $actual = $periodCurrency !== null
                    ? (float) $rows->sum(fn (AzureSubscriptionCost $row): float => (float) $row->amount)
                    : null;

                $history = ConsolidatedCostHistory::query()->firstOrNew([
                    'scope_hash' => $scopeHash,
                    'period_start' => $period,
                ]);

                $history->fill([
                    'scope_filter' => $scopeFilter,
                    'actual_cost' => $actual,
                    'currency' => $periodCurrency,
                    'subscription_count' => count($ids),
                    'captured_at' => now(),
                ])->save();
            }

            $budgetRows = AzureBudget::query()->whereIn('subscription_id', $ids)->get(['amount', 'currency']);
            $budgetCurrencies = $budgetRows->pluck('currency')->filter()->unique();
            $budgetTotal = $budgetCurrencies->count() === 1
                ? (float) $budgetRows->sum(fn (AzureBudget $budget): float => (float) $budget->amount)
                : null;

            $forecastRows = AzureCostForecast::query()->whereIn('subscription_id', $ids)->get(['subscription_id', 'forecast_amount', 'currency']);
            $forecastCurrencies = $forecastRows->pluck('currency')->filter()->unique();
            $forecastCurrency = $forecastCurrencies->count() === 1 ? $forecastCurrencies->first() : null;

            $currentActualRows = AzureSubscriptionCost::query()
                ->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')
                ->whereDate('period_start', $currentPeriod)
                ->get(['amount', 'currency']);
            $currentCurrencies = $currentActualRows->pluck('currency')->filter()->unique();
            $currentActual = $currentCurrencies->count() === 1
                ? (float) $currentActualRows->sum(fn (AzureSubscriptionCost $row): float => (float) $row->amount)
                : null;

            $forecastTotal = null;
            if ($forecastRows->isNotEmpty() && $forecastCurrency !== null && ($currentCurrencies->isEmpty() || $currentCurrencies->contains($forecastCurrency))) {
                $forecastTotal = ($currentActual ?? 0.0)
                    + (float) $forecastRows->sum(fn (AzureCostForecast $row): float => (float) $row->forecast_amount);
            }

            $current = ConsolidatedCostHistory::query()->firstOrNew([
                'scope_hash' => $scopeHash,
                'period_start' => $currentPeriod,
            ]);

            $current->fill([
                'scope_filter' => $scopeFilter,
                'actual_cost' => $currentActual,
                'budget_total' => $budgetTotal,
                'forecast_total' => $forecastTotal,
                'currency' => $currentCurrencies->count() === 1 ? $currentCurrencies->first() : $forecastCurrency,
                'subscription_count' => count($ids),
                'captured_at' => now(),
            ])->save();

            $periods = ConsolidatedCostHistory::query()
                ->where('scope_hash', $scopeHash)
                ->orderBy('period_start')
                ->get();

            $previous = null;
            foreach ($periods as $period) {
                $difference = ($period->actual_cost !== null && $previous?->actual_cost !== null)
                    ? (float) $period->actual_cost - (float) $previous->actual_cost
                    : null;
                $percentage = ($difference !== null && (float) $previous->actual_cost !== 0.0)
                    ? ($difference / abs((float) $previous->actual_cost)) * 100
                    : null;

                $period->forceFill([
                    'mom_difference' => $difference,
                    'mom_percentage' => $percentage,
                ])->save();

                $previous = $period;
            }
        });

        return $scopeHash;
    }
}
