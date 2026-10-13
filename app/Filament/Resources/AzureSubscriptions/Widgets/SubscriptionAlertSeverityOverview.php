<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionAlertSeverityOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $counts = $this->record->alerts()
            ->selectRaw('severity, COUNT(*) as aggregate')
            ->groupBy('severity')
            ->pluck('aggregate', 'severity');

        return [
            Stat::make('Sev 0', (string) ($counts['Sev0'] ?? 0))
                ->color('danger'),
            Stat::make('Sev 1', (string) ($counts['Sev1'] ?? 0))
                ->color('danger'),
            Stat::make('Sev 2', (string) ($counts['Sev2'] ?? 0))
                ->color('warning'),
            Stat::make('Sev 3', (string) ($counts['Sev3'] ?? 0))
                ->color('warning'),
            Stat::make('Sev 4', (string) ($counts['Sev4'] ?? 0))
                ->color('gray'),
            Stat::make('Total Alerts', (string) $this->record->alerts()->count()),
        ];
    }
}
