<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserGroup;
use App\Services\MandatoryNotificationService;
use App\Services\NotificationRuleService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;

        app(NotificationRuleService::class)->trigger('user.created', [
            'user_id' => (int) $user->getKey(),
            'user_name' => (string) $user->name,
            'user_email' => (string) $user->email,
            'user_status' => $user->is_active ? 'ACTIVE' : 'INACTIVE',
        ]);

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
