<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionAlertSeverityOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 6;

    protected function getDescription(): ?string
    {
        return 'Click a severity card to filter alerts. Click Total Alerts to remove filters.';
    }

    protected function getStats(): array
    {
        $counts = $this->record->alerts()
            ->selectRaw('severity, COUNT(*) as aggregate')
            ->groupBy('severity')
            ->pluck('aggregate', 'severity');

        return [
            Stat::make('Sev 0', (string) ($counts['Sev0'] ?? 0))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev0' })",
                ]),
            Stat::make('Sev 1', (string) ($counts['Sev1'] ?? 0))
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev1' })",
                ]),
            Stat::make('Sev 2', (string) ($counts['Sev2'] ?? 0))
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev2' })",
                ]),
            Stat::make('Sev 3', (string) ($counts['Sev3'] ?? 0))
                ->color('warning')
                ->icon('heroicon-o-exclamation-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev3' })",
                ]),
            Stat::make('Sev 4', (string) ($counts['Sev4'] ?? 0))
                ->color('gray')
                ->icon('heroicon-o-information-circle')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: 'Sev4' })",
                ]),
            Stat::make('Total Alerts', (string) $this->record->alerts()->count())
                ->color('primary')
                ->icon('heroicon-o-bell-alert')
                ->extraAttributes([
                    'role' => 'button',
                    'x-on:click' => "\$dispatch('filter-alerts-by-severity', { severity: null })",
                ]),
        ];
    }
}
