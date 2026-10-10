<?php

namespace App\Filament\Resources\NotificationRules;

use App\Enums\NotificationEvent;
use App\Models\NotificationRule;
use App\Models\Team;
use App\Models\User;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotificationRuleResource extends Resource
{
    protected static ?string $model = NotificationRule::class;
    protected static ?string $navigationLabel = 'Notification Rules';
    protected static ?string $modelLabel = 'Notification Rule';
    protected static ?string $pluralModelLabel = 'Notification Rules';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifications';
    protected static ?int $navigationSort = 20;
    protected static ?string $slug = 'notification-rules';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['event_key', 'title', 'message', 'action_url'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Audience' => $record->recipient_type,
            'Severity' => $record->severity,
        ];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event')->schema([
                Select::make('event_key')->label('Event')->options(NotificationEvent::options())->required()->unique(ignoreRecord: true),
                Toggle::make('enabled')->label('Enable notification')->default(false),
            ])->columns(2),
            Section::make('Recipients')->schema([
                Select::make('recipient_type')->label('Audience')->options(['all' => 'All active users', 'teams' => 'Selected teams', 'users' => 'Selected users'])->default('all')->live()->required(),
                Select::make('recipient_ids')->label(fn ($get): string => $get('recipient_type') === 'teams' ? 'Teams' : 'Users')->multiple()->searchable()->options(function ($get): array {
                    return $get('recipient_type') === 'teams' ? Team::query()->orderBy('name')->pluck('name', 'id')->all() : User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all();
                })->visible(fn ($get): bool => in_array($get('recipient_type'), ['teams', 'users'], true))->required(fn ($get): bool => in_array($get('recipient_type'), ['teams', 'users'], true)),
            ])->columns(2),
            Section::make('Notification content')->description('Use {context.key} placeholders when the triggering event supplies matching context.')->schema([
                TextInput::make('title')->label('Title')->maxLength(150),
                Textarea::make('message')->label('Message')->rows(4)->maxLength(2000),
                Select::make('type')->options(['general' => 'General', 'announcement' => 'Announcement', 'maintenance' => 'Maintenance', 'alert' => 'Alert', 'action_required' => 'Action required'])->default('general')->required(),
                Select::make('severity')->options(['info' => 'Information', 'success' => 'Success', 'warning' => 'Warning', 'danger' => 'Critical'])->default('info')->required(),
                TextInput::make('action_label')->label('Action label')->maxLength(80),
                TextInput::make('action_url')->label('Action URL')->maxLength(2048),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('event_key')
            ->columns([
                TextColumn::make('event_key')->label('Event')->formatStateUsing(fn (NotificationEvent|string $state): string => $state instanceof NotificationEvent ? $state->label() : (NotificationEvent::tryFrom($state)?->label() ?? $state))->searchable(),
                IconColumn::make('enabled')->label('Enabled')->boolean(),
                TextColumn::make('recipient_type')->label('Audience')->badge()->formatStateUsing(fn (string $state): string => match ($state) {'all' => 'All users', 'teams' => 'Teams', 'users' => 'Selected users', default => $state}),
                TextColumn::make('severity')->badge(),
                TextColumn::make('updated_at')->label('Last updated')->dateTime('Y-m-d H:i:s')->sortable(),
            ])
            ->filters([
                SelectFilter::make('enabled')->options(['1' => 'Enabled', '0' => 'Disabled']),
            ])
            ->headerActions([
                XlsxExportAction::make()->title('Export Notification Rules')->fileName(fn (): string => 'notification-rules-'.now()->format('Y-m-d-His')),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListNotificationRules::route('/'), 'create' => Pages\CreateNotificationRule::route('/create'), 'edit' => Pages\EditNotificationRule::route('/{record}/edit')];
    }
}
