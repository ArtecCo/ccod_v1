<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use Filament\Resources\Pages\EditRecord;

class EditNotificationRule extends EditRecord
{
    protected static string $resource = NotificationRuleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->guard('developers')->id();

        return $data;
    }
}
