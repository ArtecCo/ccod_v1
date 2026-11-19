<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\UserGroup;
use App\Services\MandatoryNotificationService;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    private array $previousGroupIds = [];
    private array $previousSubscriptionIds = [];

    protected function beforeSave(): void
    {
        $this->previousGroupIds = $this->record->userGroups()->pluck('user_groups.id')->map(fn ($id): int => (int) $id)->all();
        $this->previousSubscriptionIds = $this->record->subscriptionAssignments()->pluck('subscription_id')->map(fn ($id): string => (string) $id)->all();
    }

    protected function afterSave(): void
    {
        $project = $this->record;
        $service = app(MandatoryNotificationService::class);

        $currentGroupIds = $project->userGroups()->pluck('user_groups.id')->map(fn ($id): int => (int) $id)->all();
        foreach (array_diff($currentGroupIds, $this->previousGroupIds) as $groupId) {
            $group = UserGroup::query()->find($groupId);
            if ($group) {
                $service->projectGroupChanged($group, $project, true);
            }
        }
        foreach (array_diff($this->previousGroupIds, $currentGroupIds) as $groupId) {
            $group = UserGroup::query()->find($groupId);
            if ($group) {
                $service->projectGroupChanged($group, $project, false);
            }
        }

        $currentSubscriptionIds = $project->subscriptionAssignments()->pluck('subscription_id')->map(fn ($id): string => (string) $id)->all();
        foreach (array_diff($currentSubscriptionIds, $this->previousSubscriptionIds) as $subscriptionId) {
            $service->projectSubscriptionChanged($project, $subscriptionId, true);
        }
        foreach (array_diff($this->previousSubscriptionIds, $currentSubscriptionIds) as $subscriptionId) {
            $service->projectSubscriptionChanged($project, $subscriptionId, false);
        }
    }

    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('index');
    }
}
