<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Services\MandatoryNotificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $service = app(MandatoryNotificationService::class);
        $this->record->load('userGroups');

        foreach ($this->record->userGroups as $group) {
            $service->projectGroupChanged($group, $this->record, true);
        }

        foreach ($this->record->subscriptionAssignments()->pluck('subscription_id') as $subscriptionId) {
            $service->projectSubscriptionChanged($this->record, (string) $subscriptionId, true);
        }
    }

    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('index');
    }
}
