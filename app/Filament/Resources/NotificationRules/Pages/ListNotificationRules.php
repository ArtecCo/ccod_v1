<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationRules extends ListRecords
{
    protected static string $resource = NotificationRuleResource::class;

    public function getSubheading(): ?string
    {
        return 'Configured delivery rules only. The event catalogue is available when creating a rule; webhook endpoints are registered separately under Webhook Destinations.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
