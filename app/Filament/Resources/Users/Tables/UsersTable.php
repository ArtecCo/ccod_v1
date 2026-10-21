<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('User')->searchable()->sortable()->weight('medium'),
            TextColumn::make('email')->searchable()->sortable()->copyable(),
            TextColumn::make('userGroups.name')->label('User Groups')->listWithLineBreaks()->wrap(),
            IconColumn::make('is_active')->boolean()->label('Active')->alignCenter(),
            TextColumn::make('created_at')->dateTime()->sortable()->label('Activation Date')->sinceTooltip(),
        ]);
    }
}
