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
                TextColumn::make('display_name')
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
                TextColumn::make('unhealthy_resource_count')
                    ->label('Unhealthy')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->label('Weight')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->suffix(' pts'),
                TextColumn::make('percentage_score')
                    ->label('Score')
                    ->formatStateUsing(fn (mixed $state): string => round(((float) $state) * 100, 2) . '%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('healthy_resource_count')
                    ->label('Healthy')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('not_applicable_resource_count')
                    ->label('Not Applicable')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
