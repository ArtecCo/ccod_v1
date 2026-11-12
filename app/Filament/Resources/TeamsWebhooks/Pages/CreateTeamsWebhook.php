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
        $data['message_format'] = $data['message_format'] ?? 'adaptive_card';
        $template = TeamsWebhookTemplate::query()->where('category', $category)->where('is_default', true)->first()
            ?? TeamsWebhookTemplate::query()->where('category', $category)->orderBy('id')->first();
        $data['template_id'] = $data['template_id'] ?? $template?->id;
        $data['title_template'] = $template?->title_template ?? TeamsWebhookResource::defaultTitleTemplate($category);
        $data['message_template'] = $template?->message_template ?? TeamsWebhookResource::defaultMessageTemplate($category);

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return TeamsWebhookResource::getUrl('index');
    }

}
