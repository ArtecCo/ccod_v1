<?php

namespace App\Filament\Resources\NotificationRules;

use App\Enums\NotificationEvent;
use App\Models\NotificationRule;
use App\Models\Team;
use App\Models\TeamsWebhook;
use App\Models\User;
use App\Models\UserGroup;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Forms\Components\Repeater;
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
        return ['Channel' => $record->channel ?? 'in_app', 'Audience' => $record->recipient_type, 'Severity' => $record->severity];
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event and delivery')->schema([
                Select::make('event_key')
                    ->label('Event')
                    ->options(\App\Support\NotificationEventCatalog::groupedOptions())
                    ->searchable()
                    ->required(),
                Select::make('channel')
                    ->label('Delivery channel')
                    ->options(['in_app' => 'In-app notification', 'webhook' => 'Webhook'])
                    ->default('in_app')
                    ->required()
                    ->live(),
                Toggle::make('enabled')->label('Enable rule')->default(false),
            ])->columns(3),

            Section::make('Recipients')->schema([
                Select::make('recipient_type')
                    ->label('Audience')
                    ->options([
                        'all' => 'All active users',
                        'user_groups' => 'Selected user groups',
                        'teams' => 'Legacy teams',
                        'users' => 'Selected users',
                    ])
                    ->default('all')
                    ->live()
                    ->required(fn ($get): bool => $get('channel') === 'in_app')
                    ->visible(fn ($get): bool => $get('channel') === 'in_app'),
                Select::make('recipient_ids')
                    ->label(fn ($get): string => match ($get('recipient_type')) {
                        'user_groups' => 'User groups',
                        'teams' => 'Teams',
                        default => 'Users',
                    })
                    ->multiple()
                    ->searchable()
                    ->options(fn ($get): array => match ($get('recipient_type')) {
                        'user_groups' => UserGroup::query()->orderBy('name')->pluck('name', 'id')->all(),
                        'teams' => Team::query()->orderBy('name')->pluck('name', 'id')->all(),
                        default => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all(),
                    })
                    ->visible(fn ($get): bool => $get('channel') === 'in_app' && in_array($get('recipient_type'), ['user_groups', 'teams', 'users'], true))
                    ->required(fn ($get): bool => $get('channel') === 'in_app' && in_array($get('recipient_type'), ['user_groups', 'teams', 'users'], true)),
                Select::make('webhook_id')
                    ->label('Saved webhook destination')
                    ->options(fn (): array => TeamsWebhook::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(fn ($get): bool => $get('channel') === 'webhook')
                    ->visible(fn ($get): bool => $get('channel') === 'webhook'),
            ])->columns(2),

            Section::make('Conditions')
                ->description('Conditions are evaluated against the event context before delivery. Leave empty to match every occurrence.')
                ->schema([
                    Select::make('condition_mode')
                        ->label('Condition matching')
                        ->options(['all' => 'All conditions must match', 'any' => 'Any condition may match'])
                        ->default('all')
                        ->required(),
                    Repeater::make('conditions')
                        ->schema([
                            Select::make('field')
                                ->label('Event field')
                                ->options(fn ($get): array => \App\Support\NotificationEventCatalog::fieldOptions($get('../../event_key')))
                                ->searchable()
                                ->required(),
                            Select::make('operator')
                                ->label('Operator')
                                ->options(fn ($get): array => \App\Support\NotificationEventCatalog::operatorsForField($get('../../event_key'), $get('field')))
                                ->default('equals')
                                ->required(),
                            TextInput::make('value_text')->label('Expected value')->maxLength(500),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->collapsible(),
                ]),

            Section::make('In-app notification content')
                ->description('Use {context.key} placeholders when the triggering event supplies matching context.')
                ->visible(fn ($get): bool => $get('channel') === 'in_app')
                ->schema([
                    TextInput::make('title')->label('Title')->maxLength(150),
                    Textarea::make('message')->label('Message')->rows(4)->maxLength(2000),
                    Select::make('type')->options([
                        'general' => 'General',
                        'announcement' => 'Announcement',
                        'maintenance' => 'Maintenance',
                        'alert' => 'Alert',
                        'action_required' => 'Action required',
                    ])->default('general')->required(),
                    Select::make('severity')->options([
                        'info' => 'Information',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                    ])->default('info')->required(),
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
                TextColumn::make('event_key')
                    ->label('Event')
                    ->formatStateUsing(fn (NotificationEvent|string $state): string => $state instanceof NotificationEvent ? $state->label() : (NotificationEvent::tryFrom($state)?->label() ?? $state))
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('enabled')->label('Enabled')->boolean()->toggleable(),
                TextColumn::make('channel')->label('Channel')->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'webhook' ? 'Webhook' : 'In-app')
                    ->toggleable(),
                TextColumn::make('recipient_type')->label('Audience')->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'all' => 'All users',
                        'user_groups' => 'User groups',
                        'teams' => 'Legacy teams',
                        'users' => 'Selected users',
                        default => 'Webhook destination',
                    })->toggleable(),
                TextColumn::make('severity')->badge()->toggleable(),
                TextColumn::make('updated_at')->label('Last updated')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(),
            ])
            ->filters([SelectFilter::make('enabled')->options(['1' => 'Enabled', '0' => 'Disabled'])])
            ->headerActions([
                XlsxExportAction::make()->title('Export Notification Rules')->fileName(fn (): string => 'notification-rules-'.now()->format('Y-m-d-His')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationRules::route('/'),
            'create' => Pages\CreateNotificationRule::route('/create'),
            'edit' => Pages\EditNotificationRule::route('/{record}/edit'),
        ];
    }
}
