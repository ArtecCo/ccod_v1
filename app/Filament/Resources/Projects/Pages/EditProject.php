<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\ProjectSubscription;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function afterSave(): void
    {
        ProjectSubscription::where('project_id', $this->record->getKey())->delete();
        foreach (($this->data['subscriptionAssignments'] ?? []) as $environment => $subscriptionId) {
            if (blank($subscriptionId)) continue;
            ProjectSubscription::create(['project_id'=>$this->record->getKey(),'subscription_id'=>$subscriptionId,'environment'=>$environment]);
        }
    }
}
