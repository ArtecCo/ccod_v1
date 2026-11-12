<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use App\Models\TeamsWebhookTemplate;
use App\Support\TeamsWebhookEvents;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhook extends CreateRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $template = TeamsWebhookTemplate::query()->find($data['template_id'] ?? null);

        if ($template) {
            $data['category'] = $template->category;
            $data['title_template'] = $template->title_template;
            $data['message_template'] = $template->message_template;
        } else {
            $category = $data['category'] ?? 'server_start';
            $data['category'] = $category;
            $data['title_template'] = TeamsWebhookResource::defaultTitleTemplate($category);
            $data['message_template'] = TeamsWebhookResource::defaultMessageTemplate($category);
        }

        $data['event_keys'] = $data['event_keys'] ?: TeamsWebhookEvents::defaultsForCategory($data['category']);
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
