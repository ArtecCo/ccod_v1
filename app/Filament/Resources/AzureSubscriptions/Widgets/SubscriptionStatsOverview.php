<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected function getStats(): array
    {
        return [
            Stat::make('Security Score', $this->record->security_score !== null ? $this->record->security_score . '%' : '—'),
            Stat::make('Health', $this->record->health_status ?: '—')
                ->color(match (strtolower($this->record->health_status ?? '')) {
                    'healthy' => 'success',
                    'warning' => 'warning',
                    'critical', 'unhealthy' => 'danger',
                    default => 'gray',
                }),
            Stat::make('Month-to-Date Cost', $this->record->mtd_spend_eur !== null ? '€' . number_format((float) $this->record->mtd_spend_eur, 2) : '—'),
        ];
    }
}
