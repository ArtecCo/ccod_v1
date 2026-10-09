<?php

namespace App\Filament\Resources\Documentations\Tables;

use App\Filament\Resources\Documentations\DocumentationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record): string => DocumentationResource::getUrl('design', ['record' => $record])),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
