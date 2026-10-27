<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUserGroup extends EditRecord
{
    protected static string $resource = UserGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => UserGroupResource::canDelete($this->record))
                ->requiresConfirmation()
                ->modalHeading('Delete user group')
                ->modalDescription('Deleting this group will remove its user and project assignments. This action cannot be undone.')
                ->modalSubmitActionLabel('Delete group')
                ->action(function (): void {
                    $record = $this->record;

                    if (! UserGroupResource::canDelete($record)) {
                        return;
                    }

                    $record->users()->detach();
                    $record->projects()->detach();
                    $record->delete();

                    Notification::make()
                        ->success()
                        ->title('User group deleted')
                        ->send();

                    $this->redirect(UserGroupResource::getUrl('index'));
                }),
        ];
    }
}
