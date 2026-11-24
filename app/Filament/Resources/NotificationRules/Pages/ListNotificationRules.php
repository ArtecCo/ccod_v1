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
            Action::make('eventClassification')
                ->label('Event Classification')
                ->icon('heroicon-o-shield-exclamation')
                ->color('primary')
                ->modalHeading('Event Classification')
                ->modalDescription('Classification is independent of notification delivery. Configure how many days remain before an SSL certificate expires to classify the event as Attention, Warning, or Critical. Notification Rules use the resulting severity to decide whether and where to deliver it.')
                ->modalSubmitActionLabel('Save Classification')
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
                                ->label('Days remaining')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->maxValue(3650)
                                ->required(),
                            Select::make('severity')
                                ->label('Classify event as')
                                ->options([
                                    'reminder' => 'Attention',
                                    'warning' => 'Warning',
                                    'critical' => 'Critical',
                                ])
                                ->default('reminder')
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Add classification')
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
                            'rules' => 'Each days-remaining classification threshold must be unique.',
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
                                'webhook_ids' => [],
                                'enabled' => true,
                            ],
                        );
                        $keepIds[] = $rule->id;
                    }

                    SslReminderRule::query()->whereNotIn('id', $keepIds)->update(['enabled' => false]);

                    Notification::make()
                        ->title('Event classification saved')
                        ->body(count($keepIds).' classification threshold(s) configured.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
