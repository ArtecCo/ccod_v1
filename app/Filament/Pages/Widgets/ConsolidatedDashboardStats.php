<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureBudget;
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

class ConsolidatedDashboardStats extends BaseWidget
{
    #[Reactive]
    public string $filter = 'all';

    #[Reactive]
    public string $activeTab = 'overview';

    protected static bool $isLazy = true;
    protected ?string $pollingInterval = null;
    protected int | array | null $columns = 7;

    protected function getColumns(): int | array | null
    {
        return match ($this->activeTab) {
            'cost' => 5,
            'security' => 4,
            'resources' => 1,
            default => 7,
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
            $latestPeriod = AzureSubscriptionCost::query()->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')->max('period_start');
            $current = $latestPeriod !== null
                ? (float) AzureSubscriptionCost::query()->whereIn('subscription_id', $ids)->where('granularity', 'monthly')->whereDate('period_start', $latestPeriod)->sum('amount')
                : (float) AzureSubscription::query()->whereIn('subscription_id', $ids)->sum('mtd_spend_eur');
            $previousPeriod = $latestPeriod !== null
                ? AzureSubscriptionCost::query()->whereIn('subscription_id', $ids)->where('granularity', 'monthly')->whereDate('period_start', '<', $latestPeriod)->max('period_start')
                : null;
            $previous = $previousPeriod !== null
                ? (float) AzureSubscriptionCost::query()->whereIn('subscription_id', $ids)->where('granularity', 'monthly')->whereDate('period_start', $previousPeriod)->sum('amount')
                : null;
            $difference = $previous !== null ? $current - $previous : null;
            $differencePercent = $previous !== null && $previous != 0.0 ? ($difference / abs($previous)) * 100 : null;

            $budget = (float) AzureBudget::query()->whereIn('subscription_id', $ids)->sum('amount');
            $forecast = (float) AzureSubscription::query()->whereIn('subscription_id', $ids)
                ->with('costForecast')->get()
                ->sum(fn (AzureSubscription $subscription): float =>
                    (float) ($subscription->costs()->where('granularity', 'monthly')->orderByDesc('period_start')->value('amount') ?? 0)
                    + (float) ($subscription->costForecast?->forecast_amount ?? 0)
                );

            $trendColor = $difference === null ? 'gray' : ($difference > 0 ? 'danger' : ($difference < 0 ? 'success' : 'primary'));
            return [
                Stat::make('MTD Cost', 'EUR ' . number_format($current, 2))->icon('heroicon-o-banknotes')->color($trendColor),
                Stat::make('Budget', 'EUR ' . number_format($budget, 2))->icon('heroicon-o-wallet')->color('primary'),
                Stat::make('Forecast', 'EUR ' . number_format($forecast, 2))->icon('heroicon-o-chart-bar')->color('primary'),
                Stat::make('MoM Difference', $difference === null ? '—' : (($difference > 0 ? '+' : ($difference < 0 ? '-' : '')) . 'EUR ' . number_format(abs($difference), 2)))
                    ->description($differencePercent === null ? 'Previous month unavailable' : (($differencePercent > 0 ? '+' : '') . number_format($differencePercent, 1) . '% vs previous month'))
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
                ->selectRaw('COUNT(*) AS total, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev0, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev1, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev2, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev3, SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev4',
                    ['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'])->first();
            return [
                Stat::make('Sev 0', (string) ($counts?->sev0 ?? 0))->icon('heroicon-o-exclamation-triangle')->color('gray'),
                Stat::make('Sev 1', (string) ($counts?->sev1 ?? 0))->icon('heroicon-o-exclamation-triangle')->color('danger'),
                Stat::make('Sev 2', (string) ($counts?->sev2 ?? 0))->icon('heroicon-o-exclamation-circle')->color('warning'),
                Stat::make('Sev 3', (string) ($counts?->sev3 ?? 0))->icon('heroicon-o-exclamation-circle')->color('warning'),
                Stat::make('Sev 4', (string) ($counts?->sev4 ?? 0))->icon('heroicon-o-information-circle')->color('gray'),
                Stat::make('Total Alerts', (string) ($counts?->total ?? 0))->icon('heroicon-o-bell-alert')->color('primary'),
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
