<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeamsWebhookTemplates extends ListRecords
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
