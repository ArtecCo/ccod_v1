<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserGroup;
use App\Services\MandatoryNotificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;
        $groupIds = $user->userGroups()->pluck('user_groups.id');

        foreach (UserGroup::query()->whereKey($groupIds)->get() as $group) {
            app(MandatoryNotificationService::class)->userAddedToGroup($group, [(int) $user->getKey()]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
