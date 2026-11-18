<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionsPageSyncStat;
use Filament\Resources\Pages\ListRecords;

class ListAzureSubscriptions extends ListRecords
{
    protected static string $resource = AzureSubscriptionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            SubscriptionsPageSyncStat::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
