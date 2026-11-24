<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use App\Services\MandatoryNotificationService;
use App\Services\NotificationRuleService;
use Filament\Resources\Pages\CreateRecord;

class CreateUserGroup extends CreateRecord
{
    protected static string $resource = UserGroupResource::class;

    protected function afterCreate(): void
    {
        $service = app(MandatoryNotificationService::class);
        $group = $this->record;
        $group->load('role');

        app(NotificationRuleService::class)->trigger('user_group.created', [
            'group_id' => (int) $group->getKey(),
            'group_name' => (string) $group->name,
            'role_name' => (string) ($group->role?->name ?? ''),
        ]);

        $service->userAddedToGroup($group, $group->users()->pluck('users.id')->all());

        foreach ($group->projects()->get() as $project) {
            $service->groupProjectChanged($group, $project, true);
        }
    }
}
