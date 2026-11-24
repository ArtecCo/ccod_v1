<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationRule extends CreateRecord
{
    protected static string $resource = NotificationRuleResource::class;
    protected array $webhookIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->webhookIds = array_values(array_unique(array_filter(array_map('intval', (array) ($data['webhook_ids'] ?? [])))));
        unset($data['webhook_ids']);
        $data['webhook_id'] = ($data['channel'] ?? 'in_app') === 'webhook' ? ($this->webhookIds[0] ?? null) : null;
        $data['created_by'] = auth()->guard('developers')->id();
        $data['updated_by'] = auth()->guard('developers')->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->webhooks()->sync($this->webhookIds);
    }

    protected function getRedirectUrl(): string
    {
        return NotificationRuleResource::getUrl('index');
    }
}
