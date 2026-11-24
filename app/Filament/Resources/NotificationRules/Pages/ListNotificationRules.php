<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Models\SslReminderRule;
use App\Models\TeamsWebhook;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\ValidationException;

class ListNotificationRules extends ListRecords
{
    protected static string $resource = NotificationRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sslReminders')
                ->label('SSL Reminders')
                ->icon('heroicon-o-shield-exclamation')
                ->color('primary')
                ->modalHeading('SSL Reminder Rules')
                ->modalDescription('Set the number of days before certificate expiry and choose where each reminder is delivered. Each threshold is sent once per certificate expiry date.')
                ->modalSubmitActionLabel('Save SSL Reminders')
                ->modalWidth('7xl')
                ->fillForm(fn (): array => [
                    'rules' => SslReminderRule::query()->where('enabled', true)->orderBy('threshold_days')->get()
                        ->map(fn (SslReminderRule $rule): array => [
                            'id' => $rule->id,
                            'threshold_days' => $rule->threshold_days,
                            'severity' => $rule->severity,
                            'webhook_ids' => $rule->webhook_ids ?? [],
                        ])->all(),
                ])
                ->schema([
                    Repeater::make('rules')
                        ->label('Reminder thresholds')
                        ->schema([
                            TextInput::make('threshold_days')
                                ->label('Expiring in (days)')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(3650)
                                ->required(),
                            Select::make('severity')
                                ->label('Severity')
                                ->options([
                                    'reminder' => 'Reminder (Yellow)',
                                    'warning' => 'Warning (Orange)',
                                    'critical' => 'Critical (Red)',
                                ])
                                ->default('reminder')
                                ->required(),
                            Select::make('webhook_ids')
                                ->label('Teams webhook destinations')
                                ->multiple()
                                ->searchable()
                                ->options(fn (): array => TeamsWebhook::query()->where('enabled', true)->orderBy('name')->pluck('name', 'id')->all())
                                ->required()
                                ->helperText('Select at least one enabled Teams webhook destination.'),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Add threshold')
                        ->reorderable(false)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => isset($state['threshold_days'])
                            ? 'Within '.(int) $state['threshold_days'].' days — '.ucfirst((string) ($state['severity'] ?? 'reminder'))
                            : 'New threshold')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $rows = $data['rules'] ?? [];
                    $thresholds = array_map(
                        static fn (array $row): int => (int) ($row['threshold_days'] ?? -1),
                        $rows,
                    );
                    if (count($thresholds) !== count(array_unique($thresholds))) {
                        throw ValidationException::withMessages([
                            'rules' => 'Each SSL reminder threshold must be unique.',
                        ]);
                    }

                    $keepIds = [];
                    foreach ($rows as $row) {
                        $threshold = (int) $row['threshold_days'];
                        $severity = (string) $row['severity'];
                        $webhookIds = array_values(array_unique(array_map('intval', $row['webhook_ids'] ?? [])));
                        $validWebhookIds = TeamsWebhook::query()
                            ->where('enabled', true)
                            ->whereKey($webhookIds)
                            ->pluck('id')
                            ->map(fn ($id): int => (int) $id)
                            ->all();

                        if ($validWebhookIds === [] || count($validWebhookIds) !== count($webhookIds)) {
                            throw ValidationException::withMessages([
                                'rules' => 'Every reminder must have at least one enabled Teams webhook destination.',
                            ]);
                        }

                        $rule = SslReminderRule::query()->updateOrCreate(
                            ['threshold_days' => $threshold],
                            [
                                'severity' => $severity,
                                'webhook_ids' => $validWebhookIds,
                                'enabled' => true,
                            ],
                        );
                        $keepIds[] = $rule->id;
                    }

                    SslReminderRule::query()->whereNotIn('id', $keepIds)->update(['enabled' => false]);

                    Notification::make()
                        ->title('SSL reminder rules saved')
                        ->body(count($keepIds).' threshold(s) configured.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
