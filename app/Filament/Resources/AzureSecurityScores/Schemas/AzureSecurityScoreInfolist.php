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
                        TextEntry::make('initiative_name')
                            ->label('Security Control')
                            ->columnSpan(2),
                        TextEntry::make('weight')
                            ->label('Weight')
                            ->formatStateUsing(fn (mixed $state): string => $state === null ? 'Not available' : (string) $state)
                            ->suffix(' points'),
                        TextEntry::make('current_score')
                            ->label('Current Score')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' points'),
                        TextEntry::make('max_score')
                            ->label('Maximum Score')
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' points'),
                        TextEntry::make('percentage_score')
                            ->label('Score')
                            ->formatStateUsing(fn (mixed $state): string => $state === null ? 'Not available' : round(((float) $state) * 100, 2) . '%'),
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
