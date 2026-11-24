<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use Filament\Resources\Pages\EditRecord;

class EditNotificationRule extends EditRecord
{
    protected static string $resource = NotificationRuleResource::class;
    protected array $webhookIds = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ids = $this->record->webhooks()->pluck('teams_webhooks.id')->all();
        if ($ids === [] && $this->record->webhook_id) {
            $ids = [(int) $this->record->webhook_id];
        }
        $data['webhook_ids'] = $ids;
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->webhookIds = array_values(array_unique(array_filter(array_map('intval', (array) ($data['webhook_ids'] ?? [])))));
        unset($data['webhook_ids']);
        $data['webhook_id'] = ($data['channel'] ?? 'in_app') === 'webhook' ? ($this->webhookIds[0] ?? null) : null;
        $data['updated_by'] = auth()->guard('developers')->id();

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->webhooks()->sync($this->webhookIds);
    }

    protected function getRedirectUrl(): string
    {
        return NotificationRuleResource::getUrl('index');
    }
}
