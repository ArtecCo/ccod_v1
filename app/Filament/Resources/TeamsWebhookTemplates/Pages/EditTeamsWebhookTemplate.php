<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeamsWebhookTemplate extends EditRecord
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return TeamsWebhookTemplateResource::getUrl('index');
    }
}
