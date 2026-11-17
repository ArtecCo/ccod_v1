<?php

namespace App\Livewire\Dashboard;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscriptionCost;
use App\Models\BillingResource;
use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

class ConsolidatedDashboard extends Component
{
    public string $filter = 'all';

    public string $activeTab = 'overview';

    public function updatedFilter(): void
    {
        abort_unless($this->isValidFilter($this->filter), 403);
    }

    public function setTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview', 'cost', 'alerts', 'security', 'resources'], true), 404);

        $this->activeTab = $tab;
    }

    public function render()
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $options = $this->filterOptions($user);

        if (! $this->isValidFilter($this->filter, $options)) {
            $this->filter = 'all';
        }

        $subscriptionsQuery = $this->scopedSubscriptionQuery($user);
        $subscriptionIds = (clone $subscriptionsQuery)->select('azure_subscriptions.subscription_id');
        $subscriptions = (clone $subscriptionsQuery)->with('projects.businessUnit')->get();
        $subscriptionCount = $subscriptions->count();

        $alertsQuery = AzureAlert::query()->whereIn('subscription_id', $subscriptionIds);
        $alertCounts = (clone $alertsQuery)
            ->selectRaw('severity, COUNT(*) AS aggregate')
            ->groupBy('severity')
            ->pluck('aggregate', 'severity');

        $securityQuery = AzureSecurityAssessment::query()->whereIn('subscription_id', $subscriptionIds);
        $securityStats = (clone $securityQuery)->selectRaw(
            'COUNT(*) AS total,
             SUM(CASE WHEN state = ? THEN 1 ELSE 0 END) AS unhealthy,
             SUM(CASE WHEN state = ? AND severity = ? THEN 1 ELSE 0 END) AS high',
            ['Unhealthy', 'Unhealthy', 'High'],
        )->first();

        $resourceCount = BillingResource::query()->whereIn('subscription_id', $subscriptionIds)->count();

        $currentCosts = AzureSubscriptionCost::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->whereDate('period_start', '>=', now()->startOfMonth()->toDateString())
            ->whereDate('period_start', '<=', now()->toDateString())
            ->selectRaw('currency, SUM(amount) AS total')
            ->groupBy('currency')
            ->get();

        $costTotals = $currentCosts->map(fn ($row): array => [
            'currency' => strtoupper((string) ($row->currency ?: 'EUR')),
            'amount' => (float) $row->total,
        ])->values();

        if ($costTotals->isEmpty() && $subscriptions->isNotEmpty()) {
            $costTotals = collect([[
                'currency' => 'EUR',
                'amount' => (float) $subscriptions->sum(fn ($subscription): float => (float) ($subscription->mtd_spend_eur ?? 0)),
            ]]);
        }

        $recentAlerts = (clone $alertsQuery)
            ->with('subscription:subscription_id,display_name')
            ->orderByDesc('fire_time')
            ->limit(8)
            ->get();

        $resourcesByType = BillingResource::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->selectRaw('resource_type, COUNT(*) AS aggregate')
            ->groupBy('resource_type')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get();

        return view('livewire.dashboard.consolidated-dashboard', [
            'filterOptions' => $options,
            'subscriptions' => $subscriptions,
            'subscriptionCount' => $subscriptionCount,
            'projectCount' => $subscriptions->flatMap(fn ($subscription) => $subscription->projects)->unique('id')->count(),
            'alertCounts' => $alertCounts,
            'alertTotal' => (int) $alertCounts->sum(),
            'securityStats' => $securityStats,
            'resourceCount' => $resourceCount,
            'costTotals' => $costTotals,
            'recentAlerts' => $recentAlerts,
            'resourcesByType' => $resourcesByType,
        ]);
    }

    /**
     * The filter options are derived only from projects connected to subscriptions
     * already returned by the existing subscription RBAC query.
     */
    private function filterOptions(User $user): array
    {
        $accessibleIds = app(AccessAuthorizationService::class)
            ->accessibleSubscriptionQuery($user)
            ->reorder()
            ->select('azure_subscriptions.subscription_id');

        $projects = Project::query()
            ->whereHas('subscriptions', fn (Builder $query): Builder => $query->whereIn('azure_subscriptions.subscription_id', clone $accessibleIds))
            ->with('businessUnit:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'business_unit_id']);

        $businessUnits = BusinessUnit::query()
            ->whereIn('id', $projects->pluck('business_unit_id')->filter()->unique())
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'projects' => $projects,
            'businessUnits' => $businessUnits,
        ];
    }

    private function isValidFilter(string $filter, ?array $options = null): bool
    {
        if ($filter === 'all') {
            return true;
        }

        $options ??= $this->filterOptions(auth()->guard('web')->user());

        if (preg_match('/^project:(\d+)$/', $filter, $matches) === 1) {
            return $options['projects']->contains(fn (Project $project): bool => (int) $project->getKey() === (int) $matches[1]);
        }

        if (preg_match('/^business_unit:(\d+)$/', $filter, $matches) === 1) {
            return $options['businessUnits']->contains(fn (BusinessUnit $unit): bool => (int) $unit->getKey() === (int) $matches[1]);
        }

        return false;
    }

    private function scopedSubscriptionQuery(User $user): Builder
    {
        $query = app(AccessAuthorizationService::class)->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\d+)$/', $this->filter, $matches) === 1) {
            $projectId = (int) $matches[1];

            return $query->whereHas('projects', fn (Builder $projects): Builder => $projects->whereKey($projectId));
        }

        if (preg_match('/^business_unit:(\d+)$/', $this->filter, $matches) === 1) {
            $businessUnitId = (int) $matches[1];

            return $query->whereHas('projects', fn (Builder $projects): Builder => $projects->where('business_unit_id', $businessUnitId));
        }

        return $query;
    }
}
