<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAzureSubscriptions extends ListRecords
{
    protected static string $resource = AzureSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            XlsxExportAction::make(),
            CreateAction::make(),
        ];
    }
}
