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
                ->modalDescription('Classification is independent of notification delivery. Configure SSL expiry thresholds to classify certificate-expiry events. Azure Security Score ranges are configured separately. Classification is independent of delivery; Notification Rules use the resulting severity to decide whether and where to deliver events.')
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
            Action::make('securityScoreClassification')
                ->label('Security Score Ranges')
                ->icon('heroicon-o-chart-bar')
                ->color('primary')
                ->modalHeading('Azure Security Score Classification')
                ->modalDescription('Define percentage score ranges and the severity assigned to a decreased security score. These ranges classify the event only; Notification Rules decide whether and where it is delivered.')
                ->modalSubmitActionLabel('Save Score Ranges')
                ->modalWidth('7xl')
                ->fillForm(fn (): array => [
                    'score_ranges' => \App\Models\AzureSecurityScoreClassification::query()
                        ->where('enabled', true)->orderBy('minimum_score')->get()
                        ->map(fn (\App\Models\AzureSecurityScoreClassification $range): array => [
                            'id' => $range->id,
                            'minimum_score' => $range->minimum_score,
                            'maximum_score' => $range->maximum_score,
                            'severity' => $range->severity,
                        ])->all(),
                ])
                ->schema([
                    Repeater::make('score_ranges')
                        ->hiddenLabel()
                        ->schema([
                            TextInput::make('minimum_score')->label('Minimum score (%)')->numeric()->minValue(0)->maxValue(100)->required(),
                            TextInput::make('maximum_score')->label('Maximum score (%)')->numeric()->minValue(0)->maxValue(100)->required(),
                            Select::make('severity')->label('Classify as')->options([
                                'attention' => 'Attention',
                                'warning' => 'Warning',
                                'critical' => 'Critical',
                            ])->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Add score range')
                        ->reorderable(false)
                        ->columnSpanFull()
                        ->compact(),
                ])
                ->action(function (array $data): void {
                    $rows = $data['score_ranges'] ?? [];
                    usort($rows, fn (array $a, array $b): int => (float) $a['minimum_score'] <=> (float) $b['minimum_score']);
                    $previousMaximum = null;
                    foreach ($rows as $row) {
                        $minimum = (float) $row['minimum_score'];
                        $maximum = (float) $row['maximum_score'];
                        if ($minimum > $maximum || ($previousMaximum !== null && $minimum <= $previousMaximum)) {
                            throw ValidationException::withMessages([
                                'score_ranges' => 'Score ranges must be valid, non-overlapping, and must not share boundary values.',
                            ]);
                        }
                        $previousMaximum = $maximum;
                    }

                    $keepIds = [];
                    foreach ($rows as $row) {
                        $range = \App\Models\AzureSecurityScoreClassification::query()->updateOrCreate(
                            ['minimum_score' => $row['minimum_score'], 'maximum_score' => $row['maximum_score']],
                            ['severity' => $row['severity'], 'enabled' => true],
                        );
                        $keepIds[] = $range->id;
                    }
                    \App\Models\AzureSecurityScoreClassification::query()->whereNotIn('id', $keepIds)->update(['enabled' => false]);

                    Notification::make()->title('Security score ranges saved')
                        ->body(count($keepIds).' score range(s) configured.')
                        ->success()->send();
                }),
            CreateAction::make(),
        ];
    }
}
