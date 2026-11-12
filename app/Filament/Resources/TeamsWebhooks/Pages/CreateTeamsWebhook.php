<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhook extends CreateRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $category = $data['category'] ?? 'server_start';
        $data['message_format'] = ($data['destination_type'] ?? null) === 'chat' ? 'adaptive_card' : ($data['message_format'] ?? 'adaptive_card');

        if (trim((string) ($data['title_template'] ?? '')) === '') {
            $data['title_template'] = TeamsWebhookResource::defaultTitleTemplate($category);
        }

        if (trim((string) ($data['message_template'] ?? '')) === '') {
            $data['message_template'] = TeamsWebhookResource::defaultMessageTemplate($category);
        }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return TeamsWebhookResource::getUrl('index');
    }

}
