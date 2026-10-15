<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAzureSubscription extends EditRecord
{
    protected static string $resource = AzureSubscriptionResource::class;

    public function getRelationManagers(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return AzureSubscriptionResource::getUrl('index');
    }
}
