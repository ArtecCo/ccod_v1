<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('businessUnit.name')->label('Business Unit')->sortable()->searchable(),
            TextColumn::make('subscriptionAssignments.environment')->label('Environments')->badge(),
            TextColumn::make('is_active')->label('Active')->badge(),
            TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->recordActions([ViewAction::make(), EditAction::make()])->defaultSort('name');
    }
}
