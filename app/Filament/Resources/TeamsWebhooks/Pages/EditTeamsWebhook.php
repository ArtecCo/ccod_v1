<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use App\Models\TeamsWebhookTemplate;
use App\Support\TeamsWebhookEvents;
use Filament\Resources\Pages\EditRecord;

class EditTeamsWebhook extends EditRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $template = TeamsWebhookTemplate::query()->find($data['template_id'] ?? null);

        if ($template) {
            $data['category'] = $template->category;
            $data['title_template'] = $template->title_template;
            $data['message_template'] = $template->message_template;
        }

        $data['event_keys'] = $data['event_keys'] ?? TeamsWebhookEvents::defaultsForCategory($data['category'] ?? null);
        $data['conditions'] = $data['conditions'] ?? [];
        $data['condition_mode'] = $data['condition_mode'] ?? 'all';

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
