<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhook extends CreateRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['message_format'] = ($data['destination_type'] ?? null) === 'chat' ? 'plain_text' : ($data['message_format'] ?? 'adaptive_card');

        return $data;
    }
}
