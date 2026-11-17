<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSubscription;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\Reactive;

class ConsolidatedSeverityChart extends ChartWidget
{
    #[Reactive]
    public ?string $filter = 'all';

    #[Reactive]
    public string $activeTab = 'alerts';

    protected ?string $heading = null;
    protected ?string $pollingInterval = null;
    protected ?string $maxHeight = '300px';
    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return $this->activeTab === 'security'
            ? 'Recommendations by Severity'
            : 'Alerts by Severity';
    }

    protected function getData(): array
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $query = $authorization->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\\d+)$/', (string) $this->filter, $matches) === 1) {
            $query->whereHas('projects', fn ($q) => $q->whereKey((int) $matches[1]));
        } elseif (preg_match('/^business_unit:(\\d+)$/', (string) $this->filter, $matches) === 1) {
            $query->whereHas('projects', fn ($q) => $q->where('business_unit_id', (int) $matches[1]));
        } elseif ($this->filter !== 'all') {
            abort(403);
        }

        $permission = $this->activeTab === 'security' ? 'security.view' : 'alerts.view';
        $ids = $query->get()->filter(
            fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, $permission, $subscription)
        )->pluck('subscription_id')->all();

        $severities = $this->activeTab === 'security'
            ? AzureSecurityAssessment::query()->whereIn('subscription_id', $ids)->where('state', 'Unhealthy')
                ->selectRaw('severity, COUNT(*) AS aggregate')->groupBy('severity')->pluck('aggregate', 'severity')
            : AzureAlert::query()->whereIn('subscription_id', $ids)
                ->selectRaw('severity, COUNT(*) AS aggregate')->groupBy('severity')->pluck('aggregate', 'severity');

        $labels = $this->activeTab === 'security'
            ? ['High', 'Medium', 'Low']
            : ['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'];

        return [
            'datasets' => [[
                'label' => $this->activeTab === 'security' ? 'Unhealthy recommendations' : 'Alerts',
                'data' => array_map(fn (string $severity): int => (int) ($severities[$severity] ?? 0), $labels),
                'backgroundColor' => ['#dc2626', '#f59e0b', '#64748b', '#0ea5e9', '#94a3b8'],
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
