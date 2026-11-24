<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Models\SslReminderRule;
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
                ->modalDescription('Classify SSL expiry events by days remaining. Notification Rules control whether and where each event is delivered. Each threshold is evaluated once per certificate expiry date.')
                ->modalSubmitActionLabel('Save SSL Reminders')
                ->modalWidth('7xl')
                ->fillForm(fn (): array => [
                    'rules' => SslReminderRule::query()->where('enabled', true)->orderBy('threshold_days')->get()
                        ->map(fn (SslReminderRule $rule): array => [
                            'id' => $rule->id,
                            'threshold_days' => $rule->threshold_days,
                            'severity' => $rule->severity,
                        ])->all(),
                ])
                ->schema([
                    Repeater::make('rules')
                        ->hiddenLabel()
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
                                    'reminder' => 'Attention (Yellow)',
                                    'warning' => 'Warning (Orange)',
                                    'critical' => 'Critical (Red)',
                                ])
                                ->default('reminder')
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Add threshold')
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->compact(),
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
                        $rule = SslReminderRule::query()->updateOrCreate(
                            ['threshold_days' => $threshold],
                            [
                                'severity' => $severity,
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
