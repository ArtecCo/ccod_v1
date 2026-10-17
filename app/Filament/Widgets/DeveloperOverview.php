<?php

namespace App\Filament\Widgets;

use App\Models\FailedJob;
use App\Models\QueueJob;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeveloperOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Queued Jobs', number_format(QueueJob::query()->count()))
                ->description('Jobs waiting in the database queue')
                ->icon('heroicon-o-queue-list'),
            Stat::make('Failed Jobs', number_format(FailedJob::query()->count()))
                ->description('Jobs requiring developer attention')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
