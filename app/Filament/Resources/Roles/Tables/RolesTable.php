<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('permissions_count')->counts('permissions')->label('Permissions'),
                IconColumn::make('is_system_role')->boolean()->label('System role'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('name')
            ->recordUrl(fn ($record): ?string => RoleResource::canView($record)
                ? RoleResource::getUrl('edit', ['record' => $record])
                : null)
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record): bool => RoleResource::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => RoleResource::canDelete($record)),
            ]);
    }
}
