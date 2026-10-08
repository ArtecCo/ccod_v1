<?php

namespace App\Filament\Resources\AzureSubscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AzureSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Subscription')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subscription_id')
                    ->label('Subscription ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Subscription ID copied')
                    ->copyMessageDuration(1500),

                TextColumn::make('health_status')
                    ->label('Health')
                    ->badge()
                    ->sortable(),

                TextColumn::make('security_score')
                    ->label('Security Score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('mtd_spend_eur')
                    ->label('MTD Spend')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('last_synced')
                    ->label('Last Synced')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])

            ->filters([
                //
            ])

            ->recordActions([
                ViewAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}