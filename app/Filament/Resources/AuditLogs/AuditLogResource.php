<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class AuditLogResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $modelLabel = 'Audit Log';
    protected static ?string $pluralModelLabel = 'Audit Logs';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $slug = 'audit-logs';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event', 'description', 'log_name', 'causer.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Event' => $record->event ?: '—',
            'Actor' => $record->causer?->name ?? 'System',
        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Time')->dateTime('Y-m-d H:i:s')->sortable()->searchable(),
                TextColumn::make('log_name')->label('Category')->badge()->sortable()->searchable(),
                TextColumn::make('event')->label('Event')->badge()->sortable()->searchable(),
                TextColumn::make('description')->label('Description')->wrap()->searchable()->limit(90),
                TextColumn::make('causer.name')->label('Actor')->placeholder('System')->searchable(),
                TextColumn::make('properties.success')->label('Result')->formatStateUsing(fn (mixed $state): string => $state === false || $state === '0' ? 'Failed' : 'Success')->badge()->color(fn (mixed $state): string => $state === false || $state === '0' ? 'danger' : 'success'),
                TextColumn::make('properties.failure_reason')->label('Failure Reason')->placeholder('—')->wrap()->limit(80),
                TextColumn::make('properties.status_code')->label('HTTP')->placeholder('—')->sortable(),
                TextColumn::make('properties.method')->label('Method')->badge()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('event')->options(fn (): array => self::distinctOptions('event')),
                SelectFilter::make('log_name')->label('Category')->options(fn (): array => self::distinctOptions('log_name')),
                Filter::make('result')->schema([
                    Select::make('success')->label('Result')->options(['1' => 'Success', '0' => 'Failed'])->selectablePlaceholder(),
                ])->query(fn (Builder $query, array $data): Builder => $query->when(filled($data['success'] ?? null), fn (Builder $query): Builder => $query->where('properties->success', $data['success']))),
                Filter::make('created_at')->schema([
                    DatePicker::make('from')->label('From'),
                    DatePicker::make('until')->label('Until'),
                ])->query(fn (Builder $query, array $data): Builder => $query
                    ->when(filled($data['from'] ?? null), fn (Builder $query): Builder => $query->whereDate('created_at', '>=', $data['from']))
                    ->when(filled($data['until'] ?? null), fn (Builder $query): Builder => $query->whereDate('created_at', '<=', $data['until']))),
            ])
            ->actions([
                ViewAction::make()->modalHeading('Audit Log Entry')->modalSubmitAction(false)->form([
                    \Filament\Forms\Components\Placeholder::make('event')->content(fn (Activity $record): string => $record->event ?: '—'),
                    \Filament\Forms\Components\Placeholder::make('description')->content(fn (Activity $record): string => $record->description ?: '—'),
                    \Filament\Forms\Components\Placeholder::make('actor')->content(fn (Activity $record): string => $record->causer?->name ?? 'System'),
                    \Filament\Forms\Components\Placeholder::make('result')->content(fn (Activity $record): string => data_get($record->properties, 'success') === false ? 'Failed' : 'Success'),
                    \Filament\Forms\Components\Placeholder::make('failure_reason')->label('Failure Reason')->content(fn (Activity $record): string => data_get($record->properties, 'failure_reason') ?: '—'),
                    \Filament\Forms\Components\Placeholder::make('request')->label('Request')->content(fn (Activity $record): string => sprintf('%s %s', data_get($record->properties, 'method', '—'), data_get($record->properties, 'url', '—'))),
                    \Filament\Forms\Components\Placeholder::make('timestamp')->content(fn (Activity $record): string => $record->created_at?->format('Y-m-d H:i:s') ?? '—'),
                ]),
            ])
            ->bulkActions([])
            ->recordUrl(null);
    }

    private static function distinctOptions(string $column): array
    {
        return Activity::query()->whereNotNull($column)->where($column, '!=', '')->distinct()->orderBy($column)->pluck($column, $column)->filter(fn (mixed $label): bool => filled($label))->mapWithKeys(fn (mixed $label, mixed $value): array => [(string) $value => (string) $label])->all();
    }

    public static function getPages(): array
    {
        return ['index' => ListAuditLogs::route('/')];
    }
}
