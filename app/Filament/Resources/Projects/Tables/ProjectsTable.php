<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('businessUnit.name')->label('Business Unit')->searchable()->sortable(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ]);
    }
}
