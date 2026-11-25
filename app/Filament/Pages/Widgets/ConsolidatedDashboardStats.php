<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureBudget;
use App\Models\AzureCostForecast;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\BillingResource;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Url;

class ConsolidatedDashboardStats extends BaseWidget
{
    #[Url(as: 'scope', history: true)]
    public string $filter = 'all';

    #[Reactive]
    public string $activeTab = 'overview';

    protected static bool $isLazy = true;
    protected ?string $pollingInterval = '10s';
    protected int | array | null $columns = 7;

    protected function getColumns(): int | array | null
    {
        return match ($this->activeTab) {
            'cost' => 5,
            'security' => 4,
            'alerts' => 2,
            'resources' => 1,
            default => 3,
        };
    }

    protected function getStats(): array
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn ($query) => $query->whereKey((int) $matches[1]));
        } elseif (preg_match('/^business_unit:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn ($query) => $query->where('business_unit_id', (int) $matches[1]));
        } elseif ($this->filter !== 'all') {
            abort(403);
        }

        $models = $subscriptions->get();
        $idsFor = fn (string $permission): array => $models
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription))
            ->pluck('subscription_id')->values()->all();

        if ($this->activeTab === 'cost') {
            $ids = $idsFor('cost.view');
            $currentMonth = now()->startOfMonth();
            $historyStart = $currentMonth->copy()->subMonths(11);

            // Aggregate the authorized subscriptions in SQL, bounded to 12 months.
            $costRows = AzureSubscriptionCost::query()
                ->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')
                ->whereBetween('period_start', [$historyStart->toDateString(), $currentMonth->toDateString()])
                ->selectRaw('period_start, currency, SUM(amount) AS total')
                ->groupBy('period_start', 'currency')
                ->orderBy('period_start')
                ->get();

            // Amounts in different currencies (including unknown currencies) are never added.
            $costCurrencies = $costRows->pluck('currency')->unique()->values();
            $currency = $costCurrencies->count() === 1 && $costCurrencies->first() !== null
                ? (string) $costCurrencies->first()
                : null;

            $monthlyTotals = $currency !== null
                ? $costRows->groupBy(fn ($row): string => $row->period_start->format('Y-m'))
                    ->map(fn ($rows): float => (float) $rows->first()->total)
                : collect();

            $currentKey = $currentMonth->format('Y-m');
            $previousKey = $currentMonth->copy()->subMonth()->format('Y-m');
            $current = $monthlyTotals->has($currentKey) ? $monthlyTotals->get($currentKey) : null;
            $previous = $monthlyTotals->has($previousKey) ? $monthlyTotals->get($previousKey) : null;
            $difference = $current !== null && $previous !== null ? $current - $previous : null;
            $differencePercent = $difference !== null && $previous != 0.0
                ? ($difference / abs($previous)) * 100
                : null;

            $labels = [];
            $sparkline = [];
            for ($month = $historyStart->copy(); $month->lte($currentMonth); $month->addMonth()) {
                $key = $month->format('Y-m');
                $labels[] = $month->format('M');
                $sparkline[] = $monthlyTotals->has($key) ? $monthlyTotals->get($key) : null;
            }

            $budgetRows = AzureBudget::query()
                ->whereIn('subscription_id', $ids)
                ->get(['amount', 'currency']);
            $budgetCurrencies = $budgetRows->pluck('currency')->unique();
            $budgetCurrency = $budgetCurrencies->count() === 1 && $budgetCurrencies->first() !== null
                ? (string) $budgetCurrencies->first()
                : null;
            $budget = $budgetCurrency !== null
                ? (float) $budgetRows->sum(fn (AzureBudget $row): float => (float) $row->amount)
                : null;

            $forecastRows = AzureCostForecast::query()
                ->whereIn('subscription_id', $ids)
                ->get(['forecast_amount', 'currency']);
            $forecastCurrencies = $forecastRows->pluck('currency')->unique();
            $forecastCurrency = $forecastCurrencies->count() === 1 && $forecastCurrencies->first() !== null
                ? (string) $forecastCurrencies->first()
                : null;
            $forecast = $forecastCurrency !== null && $current !== null && $currency === $forecastCurrency
                ? $current + (float) $forecastRows->sum(fn ($row): float => (float) $row->forecast_amount)
                : null;

            $trendColor = $difference === null ? 'gray' : ($difference > 0 ? 'danger' : ($difference < 0 ? 'success' : 'primary'));
            $mtdStat = Stat::make('MTD Cost', $current === null || $currency === null
                    ? '—'
                    : $currency . ' ' . number_format($current, 2))
                ->icon('heroicon-o-banknotes')
                ->color($trendColor);

            if (count(array_filter($sparkline, fn ($value): bool => $value !== null)) >= 2 && $currency !== null) {
                $mtdStat = $mtdStat->chart($sparkline)->chartColor($trendColor);
            }

            if ($differencePercent !== null) {
                $mtdStat = $mtdStat
                    ->description(($differencePercent > 0 ? '+' : '') . number_format($differencePercent, 1) . '% vs previous month')
                    ->descriptionIcon($difference > 0
                        ? 'heroicon-m-arrow-trending-up'
                        : ($difference < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-minus'))
                    ->descriptionColor($trendColor);
            } else {
                $mtdStat = $mtdStat->description('Previous month unavailable');
            }

            return [
                $mtdStat,
                Stat::make('Budget', $budget === null || $budgetCurrency === null
                        ? '—'
                        : $budgetCurrency . ' ' . number_format($budget, 2))
                    ->icon('heroicon-o-wallet')->color('primary'),
                Stat::make('Forecast', $forecast === null || $forecastCurrency === null
                        ? '—'
                        : $forecastCurrency . ' ' . number_format($forecast, 2))
                    ->description('For ' . $currentMonth->format('M Y'))
                    ->icon('heroicon-o-chart-bar')->color('primary'),
                Stat::make('MoM Difference', $difference === null || $currency === null
                        ? '—'
                        : (($difference > 0 ? '+' : ($difference < 0 ? '-' : '')) . $currency . ' ' . number_format(abs($difference), 2)))
                    ->description($differencePercent === null
                        ? 'Previous month unavailable'
                        : (($differencePercent > 0 ? '+' : '') . number_format($differencePercent, 1) . '% vs previous month total'))
                    ->descriptionColor($trendColor)->color($trendColor)->icon('heroicon-o-arrow-trending-up'),
                Stat::make('Subscriptions', (string) count($ids))->icon('heroicon-o-cloud')->color('gray'),
            ];
        }

        if ($this->activeTab === 'security') {
            $ids = $idsFor('security.view');
            $latestScores = AzureSecurityScore::query()->whereIn('subscription_id', $ids)
                ->whereIn('id', function ($query) use ($ids): void {
                    $query->selectRaw('MAX(id)')->from('azure_security_scores')
                        ->whereIn('subscription_id', $ids)->groupBy('subscription_id');
                })->get(['subscription_id', 'percentage_score']);
            $average = $latestScores->isNotEmpty()
                ? $latestScores->avg(fn ($score): float => (float) $score->percentage_score * 100)
                : null;
            $high = AzureSecurityAssessment::query()->whereIn('subscription_id', $ids)
                ->where('state', 'Unhealthy')->where('severity', 'High')->count();
            $low = $latestScores->filter(fn ($score): bool => (float) $score->percentage_score * 100 < 80)->count();

            return [
                Stat::make('Average Security Score', $average === null ? '—' : number_format($average, 2) . '%')
                    ->icon('heroicon-o-shield-check')->color($average !== null && $average < 80 ? 'danger' : 'success'),
                Stat::make('Subscriptions Below 80%', (string) $low)->icon('heroicon-o-exclamation-triangle')->color($low > 0 ? 'danger' : 'success'),
                Stat::make('High Severity Recommendations', (string) $high)->icon('heroicon-o-shield-exclamation')->color($high > 0 ? 'danger' : 'success'),
                Stat::make('Subscriptions with Score', (string) $latestScores->count())->icon('heroicon-o-cloud')->color('gray'),
            ];
        }

        if ($this->activeTab === 'alerts') {
            $ids = $idsFor('alerts.view');
            $counts = AzureAlert::query()->whereIn('subscription_id', $ids)
                ->whereIn('severity', ['Sev0', 'Sev1'])
                ->selectRaw('SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev0, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev1',
                    ['Sev0', 'Sev1'])->first();

            return [
                Stat::make('Sev 0 Alerts', number_format((int) ($counts?->sev0 ?? 0)))
                    ->icon('heroicon-o-exclamation-triangle')->color('danger'),
                Stat::make('Sev 1 Alerts', number_format((int) ($counts?->sev1 ?? 0)))
                    ->icon('heroicon-o-exclamation-triangle')->color('warning'),
            ];
        }

        if ($this->activeTab === 'resources') {
            $ids = $idsFor('resources.view');
            $count = BillingResource::query()->whereIn('subscription_id', $ids)->count();
            return [Stat::make('Total Resources', number_format($count))->icon('heroicon-o-cube')->color('primary')];
        }

        $subscriptionIds = $models->pluck('subscription_id')->all();
        $alertIds = $idsFor('alerts.view');
        $resourceIds = $idsFor('resources.view');

        return [
            Stat::make('Accessible Subscriptions', number_format(count($subscriptionIds)))->description('Within your access scope')->icon('heroicon-o-cloud')->color('primary'),
            Stat::make('Projects in Scope', number_format($models->flatMap(fn ($subscription) => $subscription->projects)->unique('id')->count()))->description('Matching selected filter')->icon('heroicon-o-rectangle-stack')->color('info'),
            Stat::make('Active Alerts', number_format(AzureAlert::query()->whereIn('subscription_id', $alertIds)->count()))->description('Only subscriptions with alert access')->icon('heroicon-o-bell-alert')->color('warning'),
            Stat::make('Resources', number_format(BillingResource::query()->whereIn('subscription_id', $resourceIds)->count()))->description('Only subscriptions with resource access')->icon('heroicon-o-cube')->color('gray'),
        ];
    }
}
