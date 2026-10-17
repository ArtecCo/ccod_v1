<?php

namespace App\Filament\Resources\QueueJobs;

use App\Models\QueueJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QueueJobResource extends Resource
{
    protected static ?string $model = QueueJob::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;
    protected static ?string $navigationLabel = 'Jobs';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 10;

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->user()?->is_active === true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('job_class')
                    ->label('Job')
                    ->state(fn (QueueJob $record): string => $record->jobClass())
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('payload', 'like', '%'.$search.'%');
                    }),
                TextColumn::make('queue')->badge()->sortable(),
                TextColumn::make('attempts')->numeric()->sortable(),
                TextColumn::make('available_at')
                    ->label('Available')
                    ->state(fn (QueueJob $record): string => date('Y-m-d H:i:s', $record->available_at)),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->state(fn (QueueJob $record): string => date('Y-m-d H:i:s', $record->created_at)),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQueueJobs::route('/'),
        ];
    }
}
