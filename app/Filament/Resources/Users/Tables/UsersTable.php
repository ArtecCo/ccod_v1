<?php

namespace App\Filament\Resources\Users\Tables;

use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('roles')
                    ->label('Roles')
                    ->state(fn ($record): array => $record->roleAssignments
                        ->map(fn ($assignment) => $assignment->role?->name)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all())
                    ->badge()
                    ->separator(', ')
                    ->wrap(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->alignCenter(),
                TextColumn::make('businessUnits.name')
                    ->label('Business Units')
                    ->listWithLineBreaks()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Activation Date')
                    ->sinceTooltip(),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                XlsxExportAction::make(),
            ]);
    }
}
