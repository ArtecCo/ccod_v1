<?php

namespace App\Filament\Resources\NotificationRules\Pages;

use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Models\AzureSecurityScoreClassification;
use App\Models\SslReminderRule;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
                ->modalDescription('Configure how SSL expiry and Azure Security Score decrease events are classified. Classification is independent of notification delivery; Notification Rules use the resulting severity to decide whether and where to deliver events.')
                ->modalSubmitActionLabel('Save Classification')
                ->modalWidth('7xl')
                ->fillForm(fn (): array => [
                    'ssl_rules' => SslReminderRule::query()->where('enabled', true)->orderBy('threshold_days')->get()
                        ->map(fn (SslReminderRule $rule): array => [
                            'id' => $rule->id,
                            'threshold_days' => $rule->threshold_days,
                            'severity' => $rule->severity,
                        ])->all(),
                    'score_ranges' => AzureSecurityScoreClassification::query()->where('enabled', true)->orderBy('minimum_score')->get()
                        ->map(fn (AzureSecurityScoreClassification $range): array => [
                            'id' => $range->id,
                            'minimum_score' => $range->minimum_score,
                            'maximum_score' => $range->maximum_score,
                            'severity' => $range->severity,
                        ])->all(),
                ])
                ->schema([
                    Grid::make(1)
                        ->schema([
                            Repeater::make('ssl_rules')
                                ->label('SSL expiry classification')
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
                                            'reminder' => 'Reminder',
                                            'reminder' => 'Reminder',
                                            'attention' => 'Attention',
                                            'warning' => 'Warning',
                                            'critical' => 'Critical',
                                        ])
                                        ->default('reminder')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel('Add SSL classification')
                                ->reorderable(false)
                                ->columnSpanFull()
                                ->compact(),

                            Repeater::make('score_ranges')
                                ->label('Azure Security Score decrease classification')
                                ->schema([
                                    TextInput::make('minimum_score')
                                        ->label('Minimum score (%)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->required(),
                                    TextInput::make('maximum_score')
                                        ->label('Maximum score (%)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->required(),
                                    Select::make('severity')
                                        ->label('Classify event as')
                                        ->options([
                                            'attention' => 'Attention',
                                            'warning' => 'Warning',
                                            'critical' => 'Critical',
                                        ])
                                        ->required(),
                                ])
                                ->columns(3)
                                ->defaultItems(0)
                                ->addActionLabel('Add score range')
                                ->reorderable(false)
                                ->columnSpanFull()
                                ->compact(),
                        ])
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    // Validate and persist both classification groups as one unit.
                    // If either group fails, neither group's changes are committed.
                    $counts = DB::transaction(function () use ($data): array {
                        $sslRows = array_values($data['ssl_rules'] ?? []);
                        $scoreRows = array_values($data['score_ranges'] ?? []);

                        $sslThresholds = array_map(
                            static fn (array $row): int => (int) ($row['threshold_days'] ?? -1),
                            $sslRows,
                        );

                        if (count($sslThresholds) !== count(array_unique($sslThresholds))) {
                            throw ValidationException::withMessages([
                                'ssl_rules' => 'Each days-remaining classification threshold must be unique.',
                            ]);
                        }

                        foreach ($sslRows as $row) {
                            $threshold = filter_var($row['threshold_days'] ?? null, FILTER_VALIDATE_INT);
                            if ($threshold === false || $threshold < 0 || $threshold > 3650
                                || ! in_array($row['severity'] ?? null, ['reminder', 'attention', 'warning', 'critical'], true)) {
                                throw ValidationException::withMessages([
                                    'ssl_rules' => 'SSL classifications must have a valid days-remaining threshold and one of the four configured severities.',
                                ]);
                            }
                        }

                        usort(
                            $scoreRows,
                            static fn (array $a, array $b): int => (float) ($a['minimum_score'] ?? 0) <=> (float) ($b['minimum_score'] ?? 0),
                        );

                        $previousMaximum = null;
                        foreach ($scoreRows as $row) {
                            $minimumValue = $row['minimum_score'] ?? null;
                            $maximumValue = $row['maximum_score'] ?? null;
                            if (! is_numeric($minimumValue) || ! is_numeric($maximumValue)) {
                                throw ValidationException::withMessages([
                                    'score_ranges' => 'Enter numeric minimum and maximum values for every score range.',
                                ]);
                            }

                            $minimum = (float) $minimumValue;
                            $maximum = (float) $maximumValue;
                            if ($minimum < 0 || $maximum > 100 || $minimum >= $maximum
                                || ($previousMaximum !== null && $minimum < $previousMaximum)
                                || ! in_array($row['severity'] ?? null, ['reminder', 'attention', 'warning', 'critical'], true)) {
                                throw ValidationException::withMessages([
                                    'score_ranges' => 'Score ranges must be between 0 and 100, have a minimum below the maximum, not overlap, and use Reminder, Attention, Warning, or Critical.',
                                ]);
                            }

                            // Adjacent ranges are valid: e.g. 70–80 and 80–85.
                            // At the shared boundary, the range with the higher minimum wins.
                            $previousMaximum = $maximum;
                        }

                        $sslKeepIds = [];
                        foreach ($sslRows as $row) {
                            $rule = SslReminderRule::query()->updateOrCreate(
                                ['threshold_days' => (int) $row['threshold_days']],
                                [
                                    'severity' => (string) $row['severity'],
                                    'webhook_ids' => [],
                                    'enabled' => true,
                                ],
                            );
                            $sslKeepIds[] = $rule->id;
                        }
                        SslReminderRule::query()->whereNotIn('id', $sslKeepIds)->update(['enabled' => false]);

                        $scoreKeepIds = [];
                        foreach ($scoreRows as $row) {
                            $range = AzureSecurityScoreClassification::query()->updateOrCreate(
                                [
                                    'minimum_score' => (float) $row['minimum_score'],
                                    'maximum_score' => (float) $row['maximum_score'],
                                ],
                                [
                                    'severity' => (string) $row['severity'],
                                    'enabled' => true,
                                ],
                            );
                            $scoreKeepIds[] = $range->id;
                        }
                        AzureSecurityScoreClassification::query()->whereNotIn('id', $scoreKeepIds)->update(['enabled' => false]);

                        return [
                            'ssl' => count($sslKeepIds),
                            'scores' => count($scoreKeepIds),
                        ];
                    });

                    Notification::make()
                        ->title('Event classification saved')
                        ->body($counts['ssl'].' SSL classification(s) and '.$counts['scores'].' score range(s) configured.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
