<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionStatsOverview extends BaseWidget
{
    public AzureSubscription $record;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 1;

    protected function getStats(): array
    {
        $urlCount = $this->record->webUrls()->count();

        return [
            Stat::make('Total URLs', number_format($urlCount))
                ->icon('heroicon-o-globe-alt')
                ->color('gray'),
        ];
    }
}
