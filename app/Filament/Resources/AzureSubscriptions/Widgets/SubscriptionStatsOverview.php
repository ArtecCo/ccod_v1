<?php

namespace App\\Filament\\Resources\\AzureSubscriptions\\Widgets;

use App\\Models\\AzureSubscription;
use Filament\\Widgets\\StatsOverviewWidget as BaseWidget;
use Filament\\Widgets\\StatsOverviewWidget\\Stat;

class SubscriptionStatsOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 3;

    protected function getStats(): array
    {
        $score = $this->record->security_score;
        $scoreValue = $score !== null ? number_format((float) $score, 2) . '%' : '—';
        $isHealthy = $score !== null && (float) $score >= 80;

        $lastSynced = $this->record->last_synced;
        $lastSyncedValue = $lastSynced !== null
            ? $lastSynced->copy()->setTimezone(config('app.timezone'))->format('d M Y, H:i')
            : '—';

        $alertCount = $this->record->alerts()->count();
        $urlCount = $this->record->webUrls()->count();

        return [
            Stat::make('Security Score', $scoreValue)
                ->icon('heroicon-o-shield-check')
                ->color($isHealthy ? 'success' : 'danger'),
            Stat::make('Health', $isHealthy ? 'Healthy' : 'Unhealthy')
                ->icon($isHealthy ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                ->color($isHealthy ? 'success' : 'danger'),
            Stat::make('MTD Cost', $this->record->mtd_spend_eur !== null
                ? '€' . number_format((float) $this->record->mtd_spend_eur, 2)
                : '—')
                ->icon('heroicon-o-banknotes')
                ->color('gray'),
            Stat::make('Last Sync', $lastSyncedValue)
                ->icon('heroicon-o-arrow-path')
                ->color('gray'),
            Stat::make('Total Alerts', number_format($alertCount))
                ->icon('heroicon-o-bell-alert')
                ->color($alertCount > 0 ? 'warning' : 'success'),
            Stat::make('Total URLs', number_format($urlCount))
                ->icon('heroicon-o-globe-alt')
                ->color('gray'),
        ];
    }
}
