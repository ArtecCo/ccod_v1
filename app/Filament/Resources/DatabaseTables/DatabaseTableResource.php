<?php

namespace App\Filament\Resources\DatabaseTables;

use App\Filament\Resources\DatabaseTables\Pages\ListDatabaseTables;
use App\Models\DatabaseTable;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DatabaseTableResource extends Resource
{
    protected static ?string $model = DatabaseTable::class;

    protected static ?string $navigationLabel = 'Database';

    protected static ?string $modelLabel = 'Table';

    protected static ?string $pluralModelLabel = 'Tables';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Database';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'database';

    public static function canViewAny(): bool
    {
        $developer = auth()->guard('developers')->user();

        return $developer !== null && $developer->is_active;
    }

    public static function getEloquentQuery(): Builder
    {
        $database = DB::connection()->getDatabaseName();

        return parent::getEloquentQuery()
            ->select([
                'TABLE_NAME as table_name',
                'TABLE_TYPE as table_type',
                'ENGINE as engine',
                'TABLE_COLLATION as table_collation',
                'TABLE_COMMENT as table_comment',
                'TABLE_ROWS as table_rows',
                'DATA_LENGTH as data_length',
                'INDEX_LENGTH as index_length',
                'CREATE_TIME as create_time',
                'UPDATE_TIME as update_time',
            ])
            ->selectRaw(<<<'SQL'
                CASE
                    WHEN TABLE_NAME LIKE 'azure_%' THEN 'Azure'
                    WHEN TABLE_NAME LIKE 'team_%' THEN 'Teams & Access'
                    WHEN TABLE_NAME LIKE 'user_%' THEN 'Users & Access'
                    WHEN TABLE_NAME LIKE 'access_%' THEN 'Users & Access'
                    WHEN TABLE_NAME LIKE 'notification%' THEN 'Notifications'
                    WHEN TABLE_NAME LIKE 'log_%' THEN 'Logging'
                    WHEN TABLE_NAME LIKE 'shiplog_%' THEN 'Maintenance'
                    WHEN TABLE_NAME LIKE 'migrations' THEN 'Laravel'
                    WHEN TABLE_NAME LIKE 'cache%' THEN 'Laravel'
                    WHEN TABLE_NAME LIKE 'jobs%' THEN 'Laravel'
                    WHEN TABLE_NAME LIKE 'failed_jobs' THEN 'Laravel'
                    WHEN TABLE_NAME LIKE 'password_reset_tokens' THEN 'Laravel'
                    WHEN TABLE_NAME LIKE 'sessions' THEN 'Laravel'
                    ELSE 'Application'
                END AS table_group
            SQL)
            ->where('TABLE_SCHEMA', $database);
    }

    public static function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('table_name')
            ->groups([
                Group::make('table_group')
                    ->label('Table group')
                    ->collapsible(),
                Group::make('engine')
                    ->label('Storage engine')
                    ->collapsible(),
            ])
            ->defaultGroup('table_group')
            ->collapsedGroupsByDefault()
            ->persistGroupInSession()
            ->columns([
                TextColumn::make('table_name')
                    ->label('Table')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('table_group')
                    ->label('Group')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('table_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('engine')
                    ->label('Engine')
                    ->sortable(),
                TextColumn::make('table_rows')
                    ->label('Rows')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('size_mb')
                    ->label('Size')
                    ->state(fn (DatabaseTable $record): float => ((float) ($record->data_length ?? 0) + (float) ($record->index_length ?? 0)) / 1024 / 1024)
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' MB')
                    ->sortable(false),
                TextColumn::make('table_collation')
                    ->label('Collation')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('table_comment')
                    ->label('Comment')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('create_time')
                    ->label('Created')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('update_time')
                    ->label('Updated')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('engine')
                    ->label('Engine')
                    ->options(fn (): array => DatabaseTable::query()
                        ->whereNotNull('engine')
                        ->distinct()
                        ->orderBy('engine')
                        ->pluck('engine', 'engine')
                        ->all()),
                SelectFilter::make('table_type')
                    ->label('Type')
                    ->options([
                        'BASE TABLE' => 'Base table',
                        'VIEW' => 'View',
                    ]),
            ])
            ->headerActions([
                XlsxExportAction::make()
                    ->title('Database Tables')
                    ->fileName(fn (): string => 'database-tables-' . now()->format('Y-m-d-His')),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDatabaseTables::route('/'),
        ];
    }
}
