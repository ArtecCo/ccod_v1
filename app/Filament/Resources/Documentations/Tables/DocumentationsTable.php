<?php

namespace App\Filament\Resources\Documentations\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordUrl(fn ($record): string => \App\Filament\Resources\Documentations\DocumentationResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->url(fn ($record): string => \App\Filament\Resources\Documentations\DocumentationResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([]);
    }
}
