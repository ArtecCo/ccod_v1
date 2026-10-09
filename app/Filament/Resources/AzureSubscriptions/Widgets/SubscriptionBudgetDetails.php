<?php

namespace App\Filament\Resources\AzureSubscriptions\Widgets;

use App\Models\AzureSubscription;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionBudgetDetails extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Budget Details';

    protected function getTableQuery(): Builder
    {
        return $this->record->budgets()->getQuery();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('budget_name')
                    ->label('Budget')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Budget Amount')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('current_spend')
                    ->label('Actual Spend')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('forecast_spend')
                    ->label('Forecast')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('time_grain')
                    ->label('Period')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('budget_name')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make(),
            ]);
    }
}
