<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use App\Services\SubscriptionModuleSyncTracker;
use App\Support\DataAge;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionAlertSeverityOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 7;

    protected function getDescription(): ?string
    {
        return 'Click a severity card to filter. Click the same card or Total Alerts to clear the filter.';
    }

    protected function getStats(): array
    {
        $counts = $this->record->alerts()
            ->selectRaw(
                'COUNT(*) AS total,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev0,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev1,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev2,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev3,
                 SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) AS sev4',
                ['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'],
            )
            ->first();

        $sync = app(SubscriptionModuleSyncTracker::class)->latest($this->record->subscription_id, 'alerts');
        $lastSynced = $sync?->last_successful_at !== null ? Carbon::parse($sync->last_successful_at) : null;
        $lastSyncedLabel = $lastSynced?->copy()->setTimezone(config('app.timezone'))->format('d M Y, H:i') ?? '—';
        [$ageDescription, $ageColor] = DataAge::for($lastSynced);

        if (($sync?->status ?? null) === 'failed') {
            $ageDescription = 'Last sync failed' . ($sync?->error_message ? ': ' . $sync->error_message : '');
            $ageColor = 'danger';
        } elseif (($sync?->status ?? null) === 'running') {
            $ageDescription = 'Sync in progress' . ($lastSynced !== null ? ' · ' . (string) $ageDescription : '');
            $ageColor = 'warning';
        }

        return [
            Stat::make('Sev 0', (string) ($counts?->sev0 ?? 0))
                ->color('gray')
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev0' })",
                ]),
            Stat::make('Sev 1', (string) ($counts?->sev1 ?? 0))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev1' })",
                ]),
            Stat::make('Sev 2', (string) ($counts?->sev2 ?? 0))
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev2' })",
                ]),
            Stat::make('Sev 3', (string) ($counts?->sev3 ?? 0))
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev3' })",
                ]),
            Stat::make('Sev 4', (string) ($counts?->sev4 ?? 0))
                ->color('gray')
                ->icon('heroicon-o-information-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev4' })",
                ]),
            Stat::make('Total Alerts', (string) ($counts?->total ?? 0))
                ->color('primary')
                ->icon('heroicon-o-bell-alert')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: null })",
                ]),
            Stat::make('Last Sync', $lastSyncedLabel)
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->description($ageDescription)
                ->descriptionColor($ageColor),
        ];
    }
}
