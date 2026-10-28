<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityScores\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AzureSecurityScoreInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Security Control')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('display_name')
                            ->label('Security Control')
                            ->columnSpan(2),
                        TextEntry::make('weight')
                            ->label('Weight')
                            ->numeric(),
                        TextEntry::make('current_score')
                            ->label('Current Score')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' points'),
                        TextEntry::make('max_score')
                            ->label('Maximum Score')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' points'),
                        TextEntry::make('potential_score_increase')
                            ->label('Potential Score Increase')
                            ->state(fn ($record): string => number_format(max(0, (float) $record->max_score - (float) $record->current_score), 2) . ' points'),
                        TextEntry::make('percentage_score')
                            ->label('Score')
                            ->formatStateUsing(fn (mixed $state): string => round(((float) $state) * 100, 2) . '%'),
                        TextEntry::make('healthy_resource_count')
                            ->label('Healthy Resources')
                            ->numeric(),
                        TextEntry::make('unhealthy_resource_count')
                            ->label('Unhealthy Resources')
                            ->numeric(),
                        TextEntry::make('not_applicable_resource_count')
                            ->label('Not Applicable')
                            ->numeric(),
                        TextEntry::make('captured_at')
                            ->label('Captured')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('synced_at')
                            ->label('Last Synced')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Not available'),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
