<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use App\Services\MandatoryNotificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateUserGroup extends CreateRecord
{
    protected static string $resource = UserGroupResource::class;

    protected function afterCreate(): void
    {
        $service = app(MandatoryNotificationService::class);
        $group = $this->record;

        $service->userAddedToGroup($group, $group->users()->pluck('users.id')->all());

        foreach ($group->projects()->get() as $project) {
            $service->groupProjectChanged($group, $project, true);
        }
    }
}
