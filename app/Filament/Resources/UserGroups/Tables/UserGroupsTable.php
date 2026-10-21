<?php

namespace App\Filament\Resources\UserGroups\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Group')->searchable()->sortable(),
                TextColumn::make('role.name')->label('Role')->badge()->sortable(),
                TextColumn::make('users_count')->counts('users')->label('Users')->sortable(),
                TextColumn::make('projects_count')->counts('projects')->label('Projects')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
