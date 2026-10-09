<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BillingResourcesRelationManager extends RelationManager
{
    protected static string $relationship = 'billingResources';

    protected static ?string $title = 'Resources';

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->select([
                'resource_id',
                'subscription_id',
                'name',
                'resource_type',
                'region',
                'cost_eur',
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('Resource Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('resource_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('region')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cost_eur')
                    ->label('Cost')
                    ->money('EUR')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('resource_type')
                    ->label('Type')
                    ->options(fn (): array => $this->getOwnerRecord()
                        ->billingResources()
                        ->distinct()
                        ->orderBy('resource_type')
                        ->pluck('resource_type', 'resource_type')
                        ->filter()
                        ->all()),
                SelectFilter::make('region')
                    ->label('Location')
                    ->options(fn (): array => $this->getOwnerRecord()
                        ->billingResources()
                        ->distinct()
                        ->orderBy('region')
                        ->pluck('region', 'region')
                        ->filter()
                        ->all()),
            ])
            ->groups([
                Group::make('resource_type')
                    ->label('Type'),
                Group::make('region')
                    ->label('Location'),
            ])
            ->defaultSort('name')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([]);
    }
}
