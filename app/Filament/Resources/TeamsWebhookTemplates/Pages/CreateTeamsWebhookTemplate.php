<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhookTemplate extends CreateRecord
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getRedirectUrl(): string
    {
        return TeamsWebhookTemplateResource::getUrl('index');
    }
}
