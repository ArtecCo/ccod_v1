<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use App\Models\Project;
use App\Services\MandatoryNotificationService;
use App\Services\NotificationRuleService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUserGroup extends EditRecord
{
    protected static string $resource = UserGroupResource::class;

    private array $previousUserIds = [];
    private array $previousProjectIds = [];
    private ?int $previousRoleId = null;

    protected function beforeSave(): void
    {
        $this->previousUserIds = $this->record->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();
        $this->previousProjectIds = $this->record->projects()->pluck('projects.id')->map(fn ($id): int => (int) $id)->all();
        $this->previousRoleId = (int) $this->record->role_id;
    }

    protected function afterSave(): void
    {
        $group = $this->record;
        $service = app(MandatoryNotificationService::class);

        $currentUserIds = $group->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();
        $addedUserIds = array_values(array_diff($currentUserIds, $this->previousUserIds));

        if ($addedUserIds !== []) {
            $service->userAddedToGroup($group, $addedUserIds);
        }

        if ($this->previousRoleId !== (int) $group->role_id) {
            $service->groupRoleChanged($group);
        }

        $currentProjectIds = $group->projects()->pluck('projects.id')->map(fn ($id): int => (int) $id)->all();
        $addedProjectIds = array_values(array_diff($currentProjectIds, $this->previousProjectIds));
        $removedProjectIds = array_values(array_diff($this->previousProjectIds, $currentProjectIds));

        foreach (Project::query()->whereKey($addedProjectIds)->get() as $project) {
            $service->groupProjectChanged($group, $project, true);
        }

        foreach (Project::query()->whereKey($removedProjectIds)->get() as $project) {
            $service->groupProjectChanged($group, $project, false);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => UserGroupResource::canDelete($this->record))
                ->requiresConfirmation()
                ->modalHeading('Delete user group')
                ->modalDescription('Deleting this group will remove its user and project assignments. This action cannot be undone.')
                ->modalSubmitActionLabel('Delete group')
                ->successNotification(null)
                ->action(function (): void {
                    $record = $this->record;

                    if (! UserGroupResource::canDelete($record)) {
                        return;
                    }

                    try {
                        app(NotificationRuleService::class)->trigger('user_group.deleted', [
                            'group_id' => (int) $record->getKey(),
                            'group_name' => (string) $record->name,
                        ]);
                    } catch (\\Throwable $exception) {
                        report($exception);
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
