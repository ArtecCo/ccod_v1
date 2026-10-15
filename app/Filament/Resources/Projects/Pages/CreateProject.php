<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ProjectSubscription;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterCreate(): void
    {
        $this->syncSubscriptions();
    }

    private function syncSubscriptions(): void
    {
        foreach (($this->data['subscriptionAssignments'] ?? []) as $environment => $subscriptionId) {
            if (blank($subscriptionId)) continue;
            ProjectSubscription::create(['project_id'=>$this->record->getKey(),'subscription_id'=>$subscriptionId,'environment'=>$environment]);
        }
    }
}
