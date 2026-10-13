<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureAlertsRelationManager extends RelationManager
{
    protected static string $relationship = 'alerts';

    protected static ?string $title = 'Alerts';

    protected static bool $isLazy = true;

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
                    ->sortable(),
                TextColumn::make('affected_resource')
                    ->label('Affected Resource')
                    ->searchable()
                    ->limit(45),
                TextColumn::make('signal_type')
                    ->label('Signal Type')
                    ->searchable()
                    ->sortable(),
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
            ->toolbarActions([]);
    }
}
