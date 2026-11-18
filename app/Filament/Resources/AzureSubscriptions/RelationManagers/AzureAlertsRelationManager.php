<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;


use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use App\Models\AzureAlert;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class AzureAlertsRelationManager extends RelationManager
{
    protected static string $relationship = 'alerts';
    protected static ?string $title = 'Alerts';
    protected static bool $isLazy = true;

    private ?array $cachedFilterOptions = null;

    #[On('filter-alerts-by-severity')]
    public function filterAlertsBySeverity(?string $severity = null): void
    {
        $currentSeverity = $this->tableFilters['severity']['value'] ?? null;
        $this->tableFilters['severity']['value'] = $currentSeverity === $severity ? null : $severity;
        $this->updatedTableFilters();
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->select(['id', 'azure_alert_id', 'name', 'severity', 'affected_resource', 'target_resource_type', 'alert_condition', 'user_response', 'monitor_service', 'signal_type', 'fire_time', 'last_modified_time', 'subscription_id', 'target_resource_group', 'suppression_status'])->with(['details', 'subscription']))
            ->columns([
                TextColumn::make('name')->label('Alert Name')->searchable()->sortable()->weight('medium')
                    ->action(fn ($record): Action => $this->alertDetailAction('viewAlertFromName', 'View alert')),
                TextColumn::make('severity')->label('Severity')->badge()->color(fn (string $state): string => match ($state) {'Sev0', 'Sev1' => 'danger', 'Sev2', 'Sev3' => 'warning', 'Sev4' => 'gray', default => 'gray'})->sortable(),
                TextColumn::make('affected_resource')->label('Affected Resource')->searchable()->limit(45),
                TextColumn::make('target_resource_type')->label('Target Resource Type')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('signal_type')->label('Signal Type')->searchable()->sortable(),
                TextColumn::make('monitor_service')->label('Monitor Service')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('target_resource_group')->label('Resource Group')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suppression_status')->label('Suppression')->badge()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('alert_condition')->label('Condition')->badge()->sortable(),
                TextColumn::make('fire_time')->label('Fire Time')->dateTime()->sortable(),
                TextColumn::make('last_modified_time')->label('Last Modified')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('severity')->options(['Sev0' => 'Sev0', 'Sev1' => 'Sev1', 'Sev2' => 'Sev2', 'Sev3' => 'Sev3', 'Sev4' => 'Sev4']),
                SelectFilter::make('signal_type')->label('Signal Type')->options(fn (): array => $this->filterOptions()['signal_type']),
                SelectFilter::make('monitor_service')->label('Monitor Service')->options(fn (): array => $this->filterOptions()['monitor_service']),
                SelectFilter::make('alert_condition')->label('Condition')->options(fn (): array => $this->filterOptions()['alert_condition']),
            ])
            ->groups([Group::make('severity')->label('Severity'), Group::make('signal_type')->label('Signal Type')])
            ->defaultSort('last_modified_time', 'desc')
            ->striped(false)
            ->recordAction('viewAlert')
            ->recordActions([$this->alertDetailAction('viewAlert', 'View')])
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExport()),
            ]);
    }

    private function alertDetailAction(string $name, string $label): ViewAction
    {
        return ViewAction::make($name)
            ->label($label)
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->modalHeading(fn (AzureAlert $record): string => $record->name ?: 'Alert details')
            ->modalWidth('7xl')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->schema([
                Section::make('Alert evaluation')
                    ->icon('heroicon-o-magnifying-glass')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')->label('Alert title')->icon('heroicon-o-bell')->columnSpanFull(),
                        TextEntry::make('severity')->badge()->color(fn (?string $state): string => match ($state) { 'Sev0', 'Sev1' => 'danger', 'Sev2', 'Sev3' => 'warning', default => 'gray' }),
                        TextEntry::make('azure_alert_id')->label('Alert ID')->icon('heroicon-o-identification')->copyable(),
                        TextEntry::make('alert_condition')->label('Alert condition')->badge(),
                        TextEntry::make('user_response')->label('User response')->placeholder('—'),
                        TextEntry::make('fire_time')->label('Fired time')->dateTime(),
                        TextEntry::make('evaluation_start')->label('Evaluation start')->state(fn (AzureAlert $record) => $this->alertEvaluationStart($record))->dateTime()->placeholder('—'),
                        TextEntry::make('evaluation_end')->label('Evaluation end')->state(fn (AzureAlert $record) => $this->alertEvaluationEnd($record))->dateTime()->placeholder('—'),
                        TextEntry::make('aggregation')->label('Time aggregation')->state(fn (AzureAlert $record) => $this->alertAggregation($record))->placeholder('—'),
                        TextEntry::make('rule')->label('Alert rule')->state(fn (AzureAlert $record) => $this->alertRule($record))->placeholder('—'),
                        TextEntry::make('value')->label('Value')->state(fn (AzureAlert $record) => $this->alertValue($record))->placeholder('—'),
                        TextEntry::make('threshold')->label('Threshold')->state(fn (AzureAlert $record) => $this->alertThreshold($record))->placeholder('—'),
                        TextEntry::make('deviation')->label('Deviation')->state(fn (AzureAlert $record) => $this->alertDeviation($record))->placeholder('—'),
                    ]),
                Section::make('Basic alert details')
                    ->icon('heroicon-o-information-circle')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('monitor_service')->label('Monitor service')->icon('heroicon-o-cog-6-tooth'),
                        TextEntry::make('signal_type')->label('Signal type')->icon('heroicon-o-signal'),
                        TextEntry::make('subscription.display_name')->label('Subscription')->icon('heroicon-o-cloud'),
                        TextEntry::make('affected_resource')->label('Affected resource')->icon('heroicon-o-computer-desktop')->copyable(),
                        TextEntry::make('target_resource_group')->label('Resource group')->icon('heroicon-o-folder'),
                        TextEntry::make('target_resource_type')->label('Target resource type')->icon('heroicon-o-cube'),
                        TextEntry::make('suppression_status')->label('Suppression status')->icon('heroicon-o-bell-slash'),
                        TextEntry::make('last_modified_time')->label('Last modified')->dateTime()->icon('heroicon-o-clock'),
                    ]),
            ])
            ->visible(fn (AzureAlert $record): bool => $this->canViewAlert($record));
    }

    private function alertConditionData(AzureAlert $record): array
    {
        $raw = $record->details?->raw_properties ?? [];
        foreach ([
            'context.context.condition.allOf.0',
            'context.condition.allOf.0',
            'alertContext.context.condition.allOf.0',
            'alertContext.condition.allOf.0',
        ] as $path) {
            $condition = data_get($raw, $path);
            if (is_array($condition) && $condition !== []) {
                return $condition;
            }
        }

        return [];
    }

    private function alertContextData(AzureAlert $record): array
    {
        $raw = $record->details?->raw_properties ?? [];
        return data_get($raw, 'context.context')
            ?? data_get($raw, 'context')
            ?? data_get($raw, 'alertContext.context')
            ?? data_get($raw, 'alertContext')
            ?? [];
    }

    private function alertEvaluationStart(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'windowStartTime') ?? data_get($condition, 'evaluationStartTime') ?? data_get($context, 'windowStartTime');
    }

    private function alertEvaluationEnd(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'windowEndTime') ?? data_get($condition, 'evaluationEndTime') ?? data_get($context, 'windowEndTime');
    }

    private function alertAggregation(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        return data_get($condition, 'timeAggregation') ?? data_get($condition, 'aggregation');
    }

    private function alertRule(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        $context = $this->alertContextData($record);
        return data_get($condition, 'metricName') ?? data_get($condition, 'alertRule') ?? data_get($context, 'alertRule');
    }

    private function alertValue(AzureAlert $record): mixed
    {
        $condition = $this->alertConditionData($record);
        return data_get($condition, 'metricValue') ?? data_get($condition, 'value') ?? data_get($condition, 'searchResult');
    }

    private function alertThreshold(AzureAlert $record): mixed
    {
        return data_get($this->alertConditionData($record), 'threshold');
    }

    private function alertDeviation(AzureAlert $record): mixed
    {
        $value = $this->alertValue($record);
        $threshold = $this->alertThreshold($record);
        return is_numeric($value) && is_numeric($threshold) ? number_format((float) $value - (float) $threshold, 2) : null;
    }

    private function canViewAlert($record): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'alerts.view', $this->getOwnerRecord());
    }

    /**
     * Load all alert filter choices with one bounded query.
     *
     * @return array<string, array<string, string>>
     */
    private function filterOptions(): array
    {
        if ($this->cachedFilterOptions !== null) {
            return $this->cachedFilterOptions;
        }

        $rows = $this->getOwnerRecord()->alerts()
            ->select(['signal_type', 'monitor_service', 'alert_condition'])
            ->distinct()
            ->get();

        $options = [];

        foreach (['signal_type', 'monitor_service', 'alert_condition'] as $field) {
            $values = $rows->pluck($field)
                ->filter(fn ($value): bool => is_string($value) && $value !== '')
                ->unique()
                ->sort()
                ->values();

            $options[$field] = $values->mapWithKeys(fn (string $value): array => [$value => $value])->all();
        }

        return $this->cachedFilterOptions = $options;
    }

    private function canExport(): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'alerts.export', $this->getOwnerRecord());
    }
}
