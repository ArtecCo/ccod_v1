<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityScores\Tables;

use App\Filament\Resources\AzureSecurityScores\AzureSecurityScoreResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AzureSecurityScoresTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('initiative_name')
                    ->label('Security Control')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('current_score')
                    ->label('Current Score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->suffix(' pts'),
                TextColumn::make('max_score')
                    ->label('Maximum Score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->suffix(' pts'),
                TextColumn::make('percentage_score')
                    ->label('Score')
                    ->formatStateUsing(fn (mixed $state): string => round(((float) $state) * 100, 2) . '%')
                    ->sortable(),
                TextColumn::make('weight')
                    ->label('Weight')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->suffix(' pts'),
                TextColumn::make('captured_at')
                    ->label('Captured')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn ($record): string => AzureSecurityScoreResource::getUrl('view', [
                'subscription' => $record->subscription_id,
                'record' => $record,
            ]))
            ->defaultSort('max_score', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }
}
