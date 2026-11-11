<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Spatie\Activitylog\Models\Activity;

class DeveloperRecentActivity extends TableWidget
{
    protected static ?string $heading = 'Recent application activity';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->with('causer'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->width('190px'),
                TextColumn::make('log_name')
                    ->label('Category')
                    ->badge()
                    ->placeholder('General')
                    ->sortable(),
                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Activity')
                    ->wrap()
                    ->searchable()
                    ->limit(110),
                TextColumn::make('causer.name')
                    ->label('Performed by')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('properties.success')
                    ->label('Result')
                    ->formatStateUsing(fn (mixed $state): string => $state === false || $state === '0' ? 'Failed' : 'Success')
                    ->badge()
                    ->color(fn (mixed $state): string => $state === false || $state === '0' ? 'danger' : 'success'),
            ])
            ->paginated([10, 25, 50])
            ->striped();
    }
}
