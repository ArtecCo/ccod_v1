<?php

namespace App\Livewire\Dashboard;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\BillingResource;
use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\Attributes\Url;

class ConsolidatedDashboard extends Component
{
    #[Url(as: 'scope', history: true)]
    public string $filter = 'all';
    public string $activeTab = 'overview';

    private const TABS = [
        'overview' => null,
        'cost' => 'cost.view',
        'alerts' => 'alerts.view',
        'security' => 'security.view',
        'resources' => 'resources.view',
    ];

    public function updatedFilter(): void
    {
        abort_unless($this->isValidFilter($this->filter), 403);
    }

    public function setTab(string $tab): void
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User && array_key_exists($tab, self::TABS), 404);
        abort_unless($this->visibleTabs($user)->contains($tab), 403);
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

        $visibleTabs = $this->visibleTabs($user);
        if (! $visibleTabs->contains($this->activeTab)) {
            $this->activeTab = 'overview';
        }

        $overviewSubscriptions = $this->scopedSubscriptionQuery($user)->with('projects.businessUnit')->get();
        $costIds = $this->permittedSubscriptionIds($user, 'cost.view');
        $alertIds = $this->permittedSubscriptionIds($user, 'alerts.view');
        $securityIds = $this->permittedSubscriptionIds($user, 'security.view');
        $resourceIds = $this->permittedSubscriptionIds($user, 'resources.view');

        $alertCounts = AzureAlert::query()->whereIn('subscription_id', $alertIds)
            ->selectRaw('severity, COUNT(*) AS aggregate')->groupBy('severity')->pluck('aggregate', 'severity');

        $recentAlerts = AzureAlert::query()->whereIn('subscription_id', $alertIds)
            ->with('subscription:subscription_id,display_name')->orderByDesc('fire_time')->limit(8)->get();

        $securityStats = AzureSecurityAssessment::query()->whereIn('subscription_id', $securityIds)
            ->selectRaw('COUNT(*) AS total,
                SUM(CASE WHEN state = ? THEN 1 ELSE 0 END) AS unhealthy,
                SUM(CASE WHEN state = ? AND severity = ? THEN 1 ELSE 0 END) AS high',
                ['Unhealthy', 'Unhealthy', 'High'])->first();

        $resourceCount = BillingResource::query()->whereIn('subscription_id', $resourceIds)->count();
        $resourcesByType = BillingResource::query()->whereIn('subscription_id', $resourceIds)
            ->selectRaw('resource_type, COUNT(*) AS aggregate')->groupBy('resource_type')
            ->orderByDesc('aggregate')->limit(8)->get();

        $currentCosts = AzureSubscriptionCost::query()->whereIn('subscription_id', $costIds)
            ->where('granularity', 'monthly')
            ->whereDate('period_start', '>=', now()->startOfMonth()->toDateString())
            ->whereDate('period_start', '<=', now()->toDateString())
            ->selectRaw('currency, SUM(amount) AS total')->groupBy('currency')->get();

        $costTotals = $currentCosts->map(fn ($row): array => [
            'currency' => strtoupper((string) ($row->currency ?: 'EUR')),
            'amount' => (float) $row->total,
        ])->values();

        if ($costTotals->isEmpty() && $costIds->isNotEmpty()) {
            $costTotals = collect([[
                'currency' => 'EUR',
                'amount' => (float) AzureSubscription::query()->whereIn('subscription_id', $costIds)->sum('mtd_spend_eur'),
            ]]);
        }

        $costSubscriptions = AzureSubscription::query()->whereIn('subscription_id', $costIds)
            ->orderBy('display_name')->get(['subscription_id', 'display_name', 'mtd_spend_eur']);

        return view('livewire.dashboard.consolidated-dashboard', [
            'filterOptions' => $options,
            'visibleTabs' => $visibleTabs,
            'subscriptions' => $overviewSubscriptions,
            'subscriptionCount' => $overviewSubscriptions->count(),
            'projectCount' => $overviewSubscriptions->flatMap(fn ($subscription) => $subscription->projects)->unique('id')->count(),
            'alertCounts' => $alertCounts,
            'alertTotal' => (int) $alertCounts->sum(),
            'securityStats' => $securityStats,
            'resourceCount' => $resourceCount,
            'costTotals' => $costTotals,
            'costSubscriptions' => $costSubscriptions,
            'recentAlerts' => $recentAlerts,
            'resourcesByType' => $resourcesByType,
        ]);
    }

    private function filterOptions(User $user): array
    {
        $accessibleIds = app(AccessAuthorizationService::class)->accessibleSubscriptionQuery($user)
            ->reorder()->select('azure_subscriptions.subscription_id');

        $projects = Project::query()
            ->whereHas('subscriptions', fn (Builder $query): Builder => $query->whereIn('azure_subscriptions.subscription_id', clone $accessibleIds))
            ->with('businessUnit:id,name')->orderBy('name')
            ->get(['id', 'name', 'business_unit_id']);

        return [
            'projects' => $projects,
            'businessUnits' => BusinessUnit::query()->whereIn('id', $projects->pluck('business_unit_id')->filter()->unique())
                ->orderBy('name')->get(['id', 'name']),
        ];
    }

    private function isValidFilter(string $filter, ?array $options = null): bool
    {
        if ($filter === 'all') return true;
        $options ??= $this->filterOptions(auth()->guard('web')->user());

        if (preg_match('/^project:(\d+)$/', $filter, $matches) === 1) {
            return $options['projects']->contains(fn (Project $project): bool => (int) $project->id === (int) $matches[1]);
        }
        if (preg_match('/^business_unit:(\d+)$/', $filter, $matches) === 1) {
            return $options['businessUnits']->contains(fn (BusinessUnit $unit): bool => (int) $unit->id === (int) $matches[1]);
        }
        return false;
    }

    private function scopedSubscriptionQuery(User $user): Builder
    {
        $query = app(AccessAuthorizationService::class)->accessibleSubscriptionQuery($user);
        if (preg_match('/^project:(\d+)$/', $this->filter, $matches) === 1) {
            return $query->whereHas('projects', fn (Builder $projects): Builder => $projects->whereKey((int) $matches[1]));
        }
        if (preg_match('/^business_unit:(\d+)$/', $this->filter, $matches) === 1) {
            return $query->whereHas('projects', fn (Builder $projects): Builder => $projects->where('business_unit_id', (int) $matches[1]));
        }
        return $query;
    }

    private function permittedSubscriptionIds(User $user, string $permission): array
    {
        $authorization = app(AccessAuthorizationService::class);
        return $this->scopedSubscriptionQuery($user)->get()
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription))
            ->pluck('subscription_id')->values()->all();
    }

    private function visibleTabs(User $user)
    {
        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $this->scopedSubscriptionQuery($user)->get();
        return collect(self::TABS)->filter(function (?string $permission, string $tab) use ($authorization, $user, $subscriptions): bool {
            return $permission === null || $subscriptions->contains(
                fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription)
            );
        })->keys()->values();
    }
}
