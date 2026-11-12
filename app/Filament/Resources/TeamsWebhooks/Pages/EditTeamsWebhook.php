<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Resources\Pages\EditRecord;

class EditTeamsWebhook extends EditRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['message_format'] = ($data['destination_type'] ?? null) === 'chat' ? 'plain_text' : ($data['message_format'] ?? 'adaptive_card');

        return $data;
    }
}
