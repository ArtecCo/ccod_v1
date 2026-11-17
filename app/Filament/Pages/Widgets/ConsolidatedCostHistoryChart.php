<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\ConsolidatedCostHistoryService;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\Url;

class ConsolidatedCostHistoryChart extends ChartWidget
{
    #[Url(as: 'scope', history: true)]
    public string $scopeFilter = 'all';

    protected ?string $heading = 'Cost Trend';

    protected string $color = 'primary';

    protected ?string $pollingInterval = null;

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user)->get()
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, 'cost.view', $subscription));

        if (preg_match('/^project:(\d+)$/', $this->scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(fn (AzureSubscription $subscription): bool => $subscription->projects()->whereKey((int) $matches[1])->exists());
        } elseif (preg_match('/^business_unit:(\d+)$/', $this->scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(fn (AzureSubscription $subscription): bool => $subscription->projects()->where('business_unit_id', (int) $matches[1])->exists());
        } elseif ($this->scopeFilter !== 'all') {
            abort(403);
        }

        $hash = app(ConsolidatedCostHistoryService::class)->capture($subscriptions, $this->scopeFilter);
        $history = \App\Models\ConsolidatedCostHistory::query()
            ->where('scope_hash', $hash)
            ->orderBy('period_start')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Actual Cost',
                    'data' => $history->map(fn ($row): ?float => $row->actual_cost !== null ? (float) $row->actual_cost : null)->all(),
                ],
                [
                    'label' => 'Budget',
                    'data' => $history->map(fn ($row): ?float => $row->budget_total !== null ? (float) $row->budget_total : null)->all(),
                ],
                [
                    'label' => 'Forecast',
                    'data' => $history->map(fn ($row): ?float => $row->forecast_total !== null ? (float) $row->forecast_total : null)->all(),
                ],
            ],
            'labels' => $history->map(fn ($row): string => $row->period_start->format('M Y'))->all(),
        ];
    }

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
            {
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => Number(value).toLocaleString(),
                        },
                    },
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'line';
    }
}
