<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
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

    #[On('filter-alerts-by-severity')]
    public function filterAlertsBySeverity(?string $severity = null): void
    {
        $this->tableFilters['severity']['value'] = $severity;
        $this->updatedTableFilters();
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->select([
                'id',
                'name',
                'severity',
                'affected_resource',
                'target_resource_type',
                'alert_condition',
                'monitor_service',
                'signal_type',
                'fire_time',
                'last_modified_time',
                'subscription_id',
                'target_resource_group',
                'suppression_status',
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Alert Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sev0', 'Sev1' => 'danger',
                        'Sev2', 'Sev3' => 'warning',
                        'Sev4' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('affected_resource')
                    ->label('Affected Resource')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('target_resource_type')
                    ->label('Target Resource Type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('signal_type')
                    ->label('Signal Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('monitor_service')
                    ->label('Monitor Service')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('target_resource_group')
                    ->label('Resource Group')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('suppression_status')
                    ->label('Suppression')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('alert_condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable(),
                TextColumn::make('fire_time')
                    ->label('Fire Time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('last_modified_time')
                    ->label('Last Modified')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('severity')
                    ->options([
                        'Sev0' => 'Sev0',
                        'Sev1' => 'Sev1',
                        'Sev2' => 'Sev2',
                        'Sev3' => 'Sev3',
                        'Sev4' => 'Sev4',
                    ]),
                SelectFilter::make('signal_type')
                    ->label('Signal Type')
                    ->options(fn (): array => $this->getOwnerRecord()
                        ->alerts()
                        ->distinct()
                        ->orderBy('signal_type')
                        ->pluck('signal_type', 'signal_type')
                        ->filter()
                        ->all()),
                SelectFilter::make('monitor_service')
                    ->label('Monitor Service')
                    ->options(fn (): array => $this->getOwnerRecord()
                        ->alerts()
                        ->distinct()
                        ->orderBy('monitor_service')
                        ->pluck('monitor_service', 'monitor_service')
                        ->filter()
                        ->all()),
                SelectFilter::make('alert_condition')
                    ->label('Condition')
                    ->options(fn (): array => $this->getOwnerRecord()
                        ->alerts()
                        ->distinct()
                        ->orderBy('alert_condition')
                        ->pluck('alert_condition', 'alert_condition')
                        ->filter()
                        ->all()),
            ])
            ->groups([
                Group::make('severity')
                    ->label('Severity'),
                Group::make('signal_type')
                    ->label('Signal Type'),
            ])
            ->defaultSort('last_modified_time', 'desc')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make(),
            ]);
    }
}
