<?php

namespace App\Filament\Resources\NotificationRules;

use App\Models\NotificationRule;
use App\Models\TeamsWebhook;
use App\Models\User;
use App\Models\UserGroup;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use App\Services\TeamsWebhookNotifier;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
        return $schema
            ->inlineLabel(false)
            ->columns(1)
            ->components([
                Section::make('Event and delivery')
                    ->columns(['default' => 1, 'lg' => 4])
                    ->schema([
                        Select::make('event_key')
                            ->label('Event')
                            ->options(\App\Support\NotificationEventCatalog::groupedOptions())
                            ->searchable()
                            ->live()
                            ->columnSpan(['lg' => 2])
                            ->afterStateUpdated(function ($state, $set): void {
                                $set('conditions', []);
                                foreach (\App\Support\NotificationEventCatalog::inAppDefaults((string) $state) as $field => $value) {
                                    $set($field, $value);
                                }
                            })
                            ->required()
                            ->helperText('Choose the event that will trigger this rule.'),
                        Select::make('channel')
                            ->label('Delivery channel')
                            ->options(['in_app' => 'In-app notification', 'webhook' => 'Webhook'])
                            ->default('in_app')
                            ->required()
                            ->live(),
                        Toggle::make('enabled')
                            ->label('Enable rule')
                            ->default(false),
                    ]),

                Section::make('Recipients')
                    ->columns(['default' => 1, 'lg' => 3])
                    ->schema([
                        Select::make('recipient_type')
                            ->label('Audience')
                            ->options([
                                'all' => 'All active users',
                                'user_groups' => 'Selected user groups',
                                'users' => 'Selected users',
                            ])
                            ->default('all')
                            ->live()
                            ->required(fn ($get): bool => $get('channel') === 'in_app')
                            ->visible(fn ($get): bool => $get('channel') === 'in_app'),
                        Select::make('recipient_ids')
                            ->label(fn ($get): string => match ($get('recipient_type')) {
                                'user_groups' => 'User groups',
                                default => 'Users',
                            })
                            ->multiple()
                            ->searchable()
                            ->options(fn ($get): array => match ($get('recipient_type')) {
                                'user_groups' => UserGroup::query()->orderBy('name')->pluck('name', 'id')->all(),
                                default => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all(),
                            })
                            ->columnSpan(['lg' => 2])
                            ->visible(fn ($get): bool => $get('channel') === 'in_app' && in_array($get('recipient_type'), ['user_groups', 'users'], true))
                            ->required(fn ($get): bool => $get('channel') === 'in_app' && in_array($get('recipient_type'), ['user_groups', 'users'], true)),
                        Select::make('webhook_id')
                            ->label('Saved webhook destination')
                            ->options(fn (): array => TeamsWebhook::query()->orderBy('name')->get()->mapWithKeys(fn (TeamsWebhook $webhook): array => [
                                $webhook->id => $webhook->name . ($webhook->description ? ' — ' . $webhook->description : ''),
                            ])->all())
                            ->searchable()
                            ->columnSpanFull()
                            ->required(fn ($get): bool => $get('channel') === 'webhook')
                            ->visible(fn ($get): bool => $get('channel') === 'webhook'),
                    ]),

                Section::make('Conditions')
                    ->description('Conditions are evaluated against the event context before delivery. Leave empty to match every occurrence.')
                    ->columns(1)
                    ->schema([
                        Select::make('condition_mode')
                            ->label('Condition matching')
                            ->options(['all' => 'All conditions must match', 'any' => 'Any condition may match'])
                            ->default('all')
                            ->required()
                            ->columnSpanFull(),
                        Repeater::make('conditions')
                            ->schema([
                                Select::make('field')
                                    ->label('Event field')
                                    ->options(fn ($get): array => \App\Support\NotificationEventCatalog::fieldOptions($get('../../event_key')))
                                    ->live()
                                    ->searchable()
                                    ->required()
                                    ->afterStateUpdated(fn ($set) => $set('value_text', null)),
                                Select::make('operator')
                                    ->label('Operator')
                                    ->options(fn ($get): array => \App\Support\NotificationEventCatalog::operatorsForField($get('../../event_key'), $get('field')))
                                    ->default('equals')
                                    ->required(),
                                Select::make('value_text')
                                    ->label('Expected value')
                                    ->options(fn ($get): array => \App\Support\NotificationEventCatalog::valuesForField($get('../../event_key'), $get('field')))
                                    ->visible(fn ($get): bool => \App\Support\NotificationEventCatalog::valuesForField($get('../../event_key'), $get('field')) !== [])
                                    ->required(fn ($get): bool => \App\Support\NotificationEventCatalog::valuesForField($get('../../event_key'), $get('field')) !== []),
                                TextInput::make('value_text')
                                    ->label('Expected value')
                                    ->visible(fn ($get): bool => \App\Support\NotificationEventCatalog::valuesForField($get('../../event_key'), $get('field')) === [])
                                    ->numeric(fn ($get): bool => in_array(\App\Support\NotificationEventCatalog::field($get('../../event_key'), $get('field'))['type'] ?? null, ['number'], true))
                                    ->maxLength(500),
                            ])
                            ->columns(['default' => 1, 'lg' => 3])
                            ->defaultItems(0)
                            ->addActionLabel('Add condition')
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Section::make('In-app notification content')
                    ->description('Use {context.key} placeholders when the triggering event supplies matching context.')
                    ->visible(fn ($get): bool => $get('channel') === 'in_app')
                    ->columns(['default' => 1, 'lg' => 2])
                    ->schema([
                        TextInput::make('title')
                            ->label('Notification title')
                            ->placeholder('Enter a short, clear title')
                            ->maxLength(150)
                            ->columnSpanFull(),
                        Textarea::make('message')
                            ->label('Notification message')
                            ->placeholder('Write the supporting details shown below the title')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('event_key')
            ->columns([
                TextColumn::make('event_key')
                    ->label('Event')
                    ->formatStateUsing(fn (?string $state): string => \App\Support\NotificationEventCatalog::event((string) $state)['label'] ?? (string) $state)
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('enabled')->label('Enabled')->boolean()->toggleable(),
                TextColumn::make('channel')->label('Channel')->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'webhook' ? 'Webhook' : 'In-app')
                    ->toggleable(),
                TextColumn::make('recipient_type')->label('Audience')->badge()
                    ->formatStateUsing(fn (?string $state, NotificationRule $record): string => $record->channel === 'webhook' ? 'Webhook' : match ($state) {
                        'all' => 'All users',
                        'user_groups' => 'User groups',
                        'users' => 'Selected users',
                        default => 'Not set',
                    })->toggleable(),
                TextColumn::make('webhook.name')->label('Destination')->placeholder('—')->toggleable(),
                TextColumn::make('severity')->badge()->formatStateUsing(fn (?string $state, NotificationRule $record): string => $record->channel === 'webhook' ? '—' : (string) $state)->toggleable(),
                TextColumn::make('updated_at')->label('Last updated')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(),
            ])
            ->filters([SelectFilter::make('enabled')->options(['1' => 'Enabled', '0' => 'Disabled'])])
            ->recordActions([
                Action::make('test_webhook')
                    ->label('Test delivery')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn (NotificationRule $record): bool => $record->channel === 'webhook')
                    ->action(function (NotificationRule $record, TeamsWebhookNotifier $notifier): void {
                        $success = $notifier->sendRuleTest($record);
                        $record->load('webhook');
                        $notification = Notification::make()
                            ->title($success ? 'Rule test delivered' : 'Rule test failed')
                            ->body($success ? 'The built-in event message was accepted by the destination.' : ($record->webhook?->last_error ?: 'Check that the destination is enabled and configured correctly.'));
                        ($success ? $notification->success() : $notification->danger())->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
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
