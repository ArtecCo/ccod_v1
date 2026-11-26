<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Services\TeamsWebhookNotifier;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_webhook')
                ->label('Test delivery')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn (): bool => $this->record->channel === 'webhook')
                ->action(function (TeamsWebhookNotifier $notifier): void {
                    $queued = $notifier->queueRuleTest($this->record);
                    $notification = Notification::make()
                        ->title($queued ? 'Rule test queued' : 'Rule test not queued')
                        ->body($queued ? 'The test message has been added to the Laravel queue. Delivery will be handled by the queue worker.' : 'Check that the destination exists and is enabled.');

                    ($queued ? $notification->success() : $notification->danger())->send();
                }),
            DeleteAction::make()->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return NotificationRuleResource::getUrl('index');
    }
}
