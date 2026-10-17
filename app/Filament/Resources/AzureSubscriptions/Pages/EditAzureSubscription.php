<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Models\AzureSubscription;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

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
            DeleteAction::make()
                ->visible(fn (): bool => AzureSubscriptionResource::canDelete($this->record))
                ->using(function (AzureSubscription $record): bool {
                    if (! AzureSubscriptionResource::canDelete($record)) {
                        throw new AuthorizationException('You are not authorized to delete this subscription.');
                    }

                    return (bool) $record->delete();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return AzureSubscriptionResource::getUrl('index');
    }
}
