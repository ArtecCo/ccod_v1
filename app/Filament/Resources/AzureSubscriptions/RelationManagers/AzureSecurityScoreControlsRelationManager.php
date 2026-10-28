<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use App\Filament\Resources\AzureSecurityScores\AzureSecurityScoreResource;
use App\Models\AzureSecurityScoreControl;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityScoreControlsRelationManager extends RelationManager
{
    protected static string $relationship = 'securityScoreControls';
    protected static ?string $title = 'Security Score Controls';
    protected static bool $isLazy = true;
    protected static ?string $relatedResource = AzureSecurityScoreResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->whereIn('id', function ($subQuery): void {
                    $subQuery->from('azure_security_score_controls as latest')
                        ->selectRaw('MAX(latest.id)')
                        ->whereColumn('latest.subscription_id', 'azure_security_score_controls.subscription_id')
                        ->whereColumn('latest.control_id', 'azure_security_score_controls.control_id')
                        ->groupBy('latest.control_id');
                }))
            ->columns([
                TextColumn::make('display_name')
                    ->label('Security Control')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),
                TextColumn::make('current_score')
                    ->label('Current')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' pts')
                    ->sortable(),
                TextColumn::make('max_score')
                    ->label('Maximum')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' pts')
                    ->sortable(),
                TextColumn::make('unhealthy_resource_count')
                    ->label('Unhealthy')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->label('Weight')
                    ->numeric()
                    ->suffix(' pts')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('percentage_score')
                    ->label('Score')
                    ->formatStateUsing(fn (mixed $state): string => round(((float) $state) * 100, 2) . '%')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (AzureSecurityScoreControl $record): string => AzureSecurityScoreResource::getUrl('view', [
                'subscription' => $record->subscription_id,
                'record' => $record,
            ]))
            ->defaultSort('max_score', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }
}
