<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResourceCostBreakdown extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Resource Cost Breakdown';

    protected function getTableQuery(): Builder
    {
        return $this->record
            ->billingResources()
            ->getQuery()
            ->select([
                'resource_id',
                'subscription_id',
                'name',
                'resource_type',
                'region',
                'cost_eur',
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
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
            ->defaultSort('name')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make(),
            ]);
    }
}
