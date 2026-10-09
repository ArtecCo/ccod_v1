<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class SubscriptionStatsOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $health = $this->record->health_status ?: '—';

        $healthColor = match (strtolower($health)) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical', 'unhealthy' => 'danger',
            default => 'gray',
        };

        $healthValue = $health === '—'
            ? '—'
            : new HtmlString("<span style=\"color: var(--{$healthColor}-600)\">" . e($health) . '</span>');

        return [
            Stat::make('Security Score', $this->record->security_score !== null ? $this->record->security_score . '%' : '—'),
            Stat::make('Health', $healthValue)
                ->color($healthColor),
            Stat::make('Month-to-Date Cost', $this->record->mtd_spend_eur !== null ? '€' . number_format((float) $this->record->mtd_spend_eur, 2) : '—'),
        ];
    }
}
