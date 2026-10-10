<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationRule extends CreateRecord
{
    protected static string $resource = NotificationRuleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->guard('developers')->id();
        $data['updated_by'] = auth()->guard('developers')->id();

        return $data;
    }
}
