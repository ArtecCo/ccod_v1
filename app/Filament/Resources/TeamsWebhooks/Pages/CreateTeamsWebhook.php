<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhook extends CreateRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // These legacy columns remain populated for backward compatibility,
        // but newly registered destinations do not subscribe to events here.
        $data['category'] = 'server_start';
        $data['template_id'] = null;
        $data['event_keys'] = null;
        $data['conditions'] = null;
        $data['condition_mode'] = 'all';
        $data['title_template'] = 'CCOD notification';
        $data['message_template'] = 'Event: {event_label}';

        $data['message_format'] = ($data['destination_type'] ?? null) === 'chat'
            ? 'adaptive_card'
            : ($data['message_format'] ?? 'adaptive_card');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return TeamsWebhookResource::getUrl('index');
    }
}
