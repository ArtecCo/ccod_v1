<?php

declare(strict_types=1);

namespace App\\Filament\\Resources\\AzureSubscriptions\\Widgets;

use App\\Models\\AzureSubscription;
use App\\Models\\AzureSubscriptionCost;
use Filament\\Tables\\Columns\\TextColumn;
use Filament\\Tables\\Table;
use Filament\\Widgets\\TableWidget as BaseWidget;
use Illuminate\\Database\\Eloquent\\Builder;

class SubscriptionCostHistoryTable extends BaseWidget
{
    public AzureSubscription $record;

    protected static bool $isLazy = true;

    protected static ?string $heading = 'Cost History Data';

    protected function getTableQuery(): Builder
    {
        return AzureSubscriptionCost::query()
            ->where('subscription_id', $this->record->subscription_id)
            ->where('granularity', 'monthly')
            ->orderBy('period_start');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')
                    ->label('Month')
                    ->date('M Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Cost')
                    ->money(fn (AzureSubscriptionCost $record): string => $record->currency ?: 'EUR')
                    ->sortable(),
            ])
            ->defaultSort('period_start')
            ->striped(false)
            ->recordActions([])
            ->paginated(false);
    }
}
