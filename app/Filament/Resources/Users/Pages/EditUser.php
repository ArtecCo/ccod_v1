<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserGroup;
use App\Services\MandatoryNotificationService;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private array $previousUserGroupIds = [];

    protected function beforeSave(): void
    {
        $this->previousUserGroupIds = $this->record->userGroups()
            ->pluck('user_groups.id')
            ->map(fn ($id): int => (int) $id)
            ->all();
    }

    protected function afterSave(): void
    {
        $user = $this->record;
        $currentGroupIds = $user->userGroups()
            ->pluck('user_groups.id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $addedGroupIds = array_values(array_diff($currentGroupIds, $this->previousUserGroupIds));

        if ($addedGroupIds === []) {
            return;
        }

        $service = app(MandatoryNotificationService::class);

        foreach (UserGroup::query()->whereKey($addedGroupIds)->get() as $group) {
            $service->userAddedToGroup($group, [(int) $user->getKey()]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
