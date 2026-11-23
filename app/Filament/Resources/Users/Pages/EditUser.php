<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\UserGroup;
use App\Services\MandatoryNotificationService;
use App\Services\NotificationRuleService;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private array $previousUserGroupIds = [];

    private bool $previousIsActive = false;

    protected function beforeSave(): void
    {
        $this->previousIsActive = (bool) $this->record->is_active;
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

        $removedGroupIds = array_values(array_diff($this->previousUserGroupIds, $currentGroupIds));
        $addedGroupIds = array_values(array_diff($currentGroupIds, $this->previousUserGroupIds));
        $ruleService = app(NotificationRuleService::class);
        $userContext = [
            'user_id' => (int) $user->getKey(),
            'user_name' => (string) $user->name,
            'user_email' => (string) $user->email,
        ];

        if ($this->previousIsActive !== (bool) $user->is_active) {
            $ruleService->trigger($user->is_active ? 'user.activated' : 'user.deactivated', $userContext + [
                'user_status' => $user->is_active ? 'ACTIVE' : 'INACTIVE',
            ]);
        }

        if ($removedGroupIds !== []) {
            foreach (UserGroup::query()->whereKey($removedGroupIds)->get() as $group) {
                $ruleService->trigger('user.group_removed', $userContext + [
                    'group_id' => (int) $group->getKey(),
                    'group_name' => (string) $group->name,
                ]);
            }
        }

        if ($addedGroupIds !== []) {
            $mandatoryService = app(MandatoryNotificationService::class);

            foreach (UserGroup::query()->whereKey($addedGroupIds)->get() as $group) {
                $mandatoryService->userAddedToGroup($group, [(int) $user->getKey()]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }
}
