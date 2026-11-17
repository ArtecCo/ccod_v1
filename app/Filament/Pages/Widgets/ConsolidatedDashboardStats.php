<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
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

    protected int | array | null $columns = 4;

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

        $models = $subscriptions->with('projects')->get();
        $projectCount = $models->flatMap(fn (AzureSubscription $subscription) => $subscription->projects)->unique('id')->count();

        $permittedIds = function (string $permission) use ($models, $authorization, $user): array {
            return $models
                ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription))
                ->pluck('subscription_id')->values()->all();
        };

        if ($this->activeTab === 'cost') {
            $ids = $permittedIds('cost.view');
            $totals = AzureSubscriptionCost::query()
                ->whereIn('subscription_id', $ids)
                ->where('granularity', 'monthly')
                ->whereDate('period_start', '>=', now()->startOfMonth()->toDateString())
                ->whereDate('period_start', '<=', now()->toDateString())
                ->selectRaw('currency, SUM(amount) AS total')
                ->groupBy('currency')->get();

            if ($totals->isEmpty() && $ids !== []) {
                return [
                    Stat::make('Month-to-date Cost', 'EUR ' . number_format((float) AzureSubscription::query()->whereIn('subscription_id', $ids)->sum('mtd_spend_eur'), 2))
                        ->icon('heroicon-o-banknotes')->color('primary'),
                    Stat::make('Subscriptions with Cost Access', (string) count($ids))
                        ->icon('heroicon-o-cloud')->color('gray'),
                ];
            }

            return $totals->map(fn ($row): Stat => Stat::make(
                'Month-to-date Cost · ' . strtoupper((string) ($row->currency ?: 'EUR')),
                number_format((float) $row->total, 2),
            )->icon('heroicon-o-banknotes')->color('primary'))->all();
        }

        if ($this->activeTab === 'alerts') {
            $ids = $permittedIds('alerts.view');
            $counts = AzureAlert::query()->whereIn('subscription_id', $ids)
                ->selectRaw('severity, COUNT(*) AS aggregate')->groupBy('severity')->pluck('aggregate', 'severity');

            return [
                Stat::make('Sev 0', (string) ($counts['Sev0'] ?? 0))->icon('heroicon-o-exclamation-triangle')->color('danger'),
                Stat::make('Sev 1', (string) ($counts['Sev1'] ?? 0))->icon('heroicon-o-exclamation-triangle')->color('danger'),
                Stat::make('Sev 2', (string) ($counts['Sev2'] ?? 0))->icon('heroicon-o-exclamation-circle')->color('warning'),
                Stat::make('Sev 3', (string) ($counts['Sev3'] ?? 0))->icon('heroicon-o-exclamation-circle')->color('warning'),
                Stat::make('Sev 4', (string) ($counts['Sev4'] ?? 0))->icon('heroicon-o-information-circle')->color('gray'),
                Stat::make('Total Alerts', (string) $counts->sum())->icon('heroicon-o-bell-alert')->color('primary'),
            ];
        }

        if ($this->activeTab === 'security') {
            $ids = $permittedIds('security.view');
            $stats = AzureSecurityAssessment::query()->whereIn('subscription_id', $ids)
                ->selectRaw('COUNT(*) AS total,
                    SUM(CASE WHEN state = ? THEN 1 ELSE 0 END) AS unhealthy,
                    SUM(CASE WHEN state = ? AND severity = ? THEN 1 ELSE 0 END) AS high',
                    ['Unhealthy', 'Unhealthy', 'High'])->first();

            return [
                Stat::make('Assessments', number_format((int) ($stats?->total ?? 0)))->icon('heroicon-o-shield-check')->color('primary'),
                Stat::make('Unhealthy', number_format((int) ($stats?->unhealthy ?? 0)))->icon('heroicon-o-exclamation-triangle')->color(((int) ($stats?->unhealthy ?? 0)) > 0 ? 'danger' : 'success'),
                Stat::make('High Severity', number_format((int) ($stats?->high ?? 0)))->icon('heroicon-o-shield-exclamation')->color(((int) ($stats?->high ?? 0)) > 0 ? 'danger' : 'success'),
                Stat::make('Subscriptions in Scope', (string) count($ids))->icon('heroicon-o-cloud')->color('gray'),
            ];
        }

        if ($this->activeTab === 'resources') {
            $ids = $permittedIds('resources.view');
            $count = BillingResource::query()->whereIn('subscription_id', $ids)->count();
            $types = BillingResource::query()->whereIn('subscription_id', $ids)
                ->selectRaw('resource_type, COUNT(*) AS aggregate')
                ->groupBy('resource_type')->orderByDesc('aggregate')->limit(3)->get();

            return [
                Stat::make('Total Resources', number_format($count))->icon('heroicon-o-cube')->color('primary'),
                ...$types->map(fn ($row): Stat => Stat::make(
                    (string) ($row->resource_type ?: 'Unclassified'),
                    number_format((int) $row->aggregate),
                )->icon('heroicon-o-cube')->color('gray'))->all(),
            ];
        }

        $subscriptionIds = $models->pluck('subscription_id')->all();
        $alertIds = $permittedIds('alerts.view');
        $resourceIds = $permittedIds('resources.view');

        return [
            Stat::make('Accessible Subscriptions', number_format(count($subscriptionIds)))
                ->description('Within your access scope')->icon('heroicon-o-cloud')->color('primary'),
            Stat::make('Projects in Scope', number_format($projectCount))
                ->description('Matching selected filter')->icon('heroicon-o-rectangle-stack')->color('info'),
            Stat::make('Active Alerts', number_format(AzureAlert::query()->whereIn('subscription_id', $alertIds)->count()))
                ->description('Only subscriptions with alert access')->icon('heroicon-o-bell-alert')->color('warning'),
            Stat::make('Resources', number_format(BillingResource::query()->whereIn('subscription_id', $resourceIds)->count()))
                ->description('Only subscriptions with resource access')->icon('heroicon-o-cube')->color('gray'),
        ];
    }
}
