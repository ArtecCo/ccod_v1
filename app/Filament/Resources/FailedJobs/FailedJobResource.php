<?php

namespace App\Filament\Resources\FailedJobs;

use App\Models\FailedJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static ?string $navigationLabel = 'Failed Jobs';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 20;

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->user()?->is_active === true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('failed_at');
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
                    ->state(fn (FailedJob $record): string => $record->jobClass())
                    ->wrap(),
                TextColumn::make('queue')->badge()->sortable(),
                TextColumn::make('failed_at')->dateTime('Y-m-d H:i:s')->sortable(),
                TextColumn::make('exception')
                    ->label('Failure')
                    ->limit(120)
                    ->wrap(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedJobs::route('/'),
        ];
    }
}
