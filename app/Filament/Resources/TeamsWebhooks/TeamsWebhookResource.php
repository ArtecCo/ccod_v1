<?php

namespace App\Filament\Resources\TeamsWebhooks;

use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookTemplate;
use App\Models\TeamsWebhookCategory;
use App\Services\TeamsWebhookNotifier;
use App\Support\TeamsWebhookEvents;
use App\Support\NotificationEventCatalog;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsWebhookResource extends Resource
{
    protected static ?string $model = TeamsWebhook::class;
    protected static ?string $navigationLabel = 'Teams Webhooks';
    protected static ?string $modelLabel = 'Teams Webhook';
    protected static ?string $pluralModelLabel = 'Teams Webhooks';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifications';
    protected static ?int $navigationSort = 30;
    protected static ?string $slug = 'teams-webhooks';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Webhook destination')->schema([
                TextInput::make('name')->required()->maxLength(120),
                Select::make('destination_type')->options(['channel' => 'Teams channel', 'chat' => 'Teams chat'])->required()->live(),
                Select::make('message_format')->options(['adaptive_card' => 'Adaptive Card', 'message_card' => 'MessageCard (legacy)'])->default('adaptive_card')->required()
                    ->visible(fn (Get $get): bool => $get('destination_type') !== 'chat'),
                TextInput::make('webhook_url')->label('Webhook URL')->url()->required()->password()->revealable()->dehydrateStateUsing(fn ($state) => trim((string) $state))->helperText('Stored encrypted.'),
                Toggle::make('enabled')->default(true)->label('Enabled'),
            ])->columns(2),

            Section::make('Event subscriptions')
                ->description('Choose one or more events that should send a notification to this destination. New event types can be added centrally without changing each webhook.')
                ->schema([
                    Select::make('event_keys')
                        ->label('Trigger events')
                        ->multiple()
                        ->options(NotificationEventCatalog::groupedOptions())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(fn (Get $get): array => TeamsWebhookEvents::defaultsForCategory($get('category')))
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('conditions', []))
                        ->helperText('A destination can subscribe to multiple events. Condition fields are derived from the selected event schemas.'),
                    Select::make('condition_mode')
                        ->label('Condition matching')
                        ->options(['all' => 'All conditions must match', 'any' => 'Any condition may match'])
                        ->default('all')
                        ->required(),
                    Repeater::make('conditions')
                        ->label('Optional event conditions')
                        ->schema([
                            Select::make('field')
                                ->label('Event field')
                                ->options(fn (Get $get): array => NotificationEventCatalog::fieldOptions($get('../../event_keys')))
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function (Set $set): void { $set('operator', 'equals'); $set('value', null); $set('value_text', null); })
                                ->required()
                                ->helperText('Only fields supplied by the selected events are listed.'),
                            Select::make('operator')
                                ->label('Operator')
                                ->options(fn (Get $get): array => NotificationEventCatalog::operatorsForField($get('../../event_keys'), $get('field')))
                                ->default('equals')
                                ->required()
                                ->live(),
                            Select::make('value')
                                ->label('Expected value')
                                ->options(fn (Get $get): array => NotificationEventCatalog::valuesForField($get('../../event_keys'), $get('field')))
                                ->searchable()
                                ->required(fn (Get $get): bool => ! in_array($get('operator'), ['exists', 'not_exists'], true) && NotificationEventCatalog::valuesForField($get('../../event_keys'), $get('field')) !== [])
                                ->visible(fn (Get $get): bool => NotificationEventCatalog::valuesForField($get('../../event_keys'), $get('field')) !== [] && ! in_array($get('operator'), ['exists', 'not_exists'], true)),
                            TextInput::make('value_text')
                                ->label('Expected value')
                                ->maxLength(500)
                                ->required(fn (Get $get): bool => ! in_array($get('operator'), ['exists', 'not_exists'], true) && NotificationEventCatalog::valuesForField($get('../../event_keys'), $get('field')) === [])
                                ->visible(fn (Get $get): bool => NotificationEventCatalog::valuesForField($get('../../event_keys'), $get('field')) === [] && ! in_array($get('operator'), ['exists', 'not_exists'], true)),
                        ])
                        ->columns(3)
                        ->addActionLabel('Add condition')
                        ->reorderable()
                        ->defaultItems(0)
                        ->collapsible()
                        ->helperText('Choose from the fields exposed by the selected events. Enum fields offer valid values; numeric fields offer comparison operators. Leave empty to receive every selected event.'),
                ]),

            Section::make('Message category and template')
                ->description('Categories organise templates. Event subscriptions decide when this destination receives a message.')
                ->schema([
                    Select::make('category')
                        ->label('Template category')
                        ->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'slug')->all())
                        ->default('server_start')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Set $set): void {
                            $template = TeamsWebhookTemplate::query()
                                ->where('category', $state)
                                ->where('is_default', true)
                                ->first()
                                ?? TeamsWebhookTemplate::query()->where('category', $state)->orderBy('id')->first();

                            $set('template_id', $template?->id);
                            $set('event_keys', TeamsWebhookEvents::defaultsForCategory($state));
                        }),
                    Select::make('template_id')
                        ->label('Message template')
                        ->options(fn (Get $get): array => TeamsWebhookTemplate::query()
                            ->where('category', $get('category') ?: 'server_start')
                            ->orderByDesc('is_default')->orderBy('name')
                            ->get()->mapWithKeys(fn (TeamsWebhookTemplate $template): array => [
                                $template->id => $template->name.($template->is_default ? ' (Default)' : ''),
                            ])->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->helperText('Template edits are shared by all destinations linked to it.'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('event_keys')->label('Subscribed events')->formatStateUsing(fn ($state): string => collect($state ?? [])->map(fn ($key) => TeamsWebhookEvents::options()[NotificationEventCatalog::normalizeKey($key)] ?? $key)->implode(', '))->wrap(),
            TextColumn::make('category')->badge()->formatStateUsing(fn (string $state): string => TeamsWebhookCategory::query()->where('slug', $state)->value('name') ?? $state),
            TextColumn::make('destination_type')->badge()->formatStateUsing(fn (string $state): string => $state === 'channel' ? 'Channel' : 'Chat'),
            IconColumn::make('enabled')->boolean(),
            TextColumn::make('last_delivery_status')->badge()->placeholder('Not sent'),
            TextColumn::make('last_error')->label('Last error')->limit(60)->placeholder('—')->toggleable(),
            TextColumn::make('last_sent_at')->dateTime('Y-m-d H:i:s')->placeholder('Never'),
        ])->recordActions([
            Action::make('test')->label('Send test')->icon('heroicon-o-paper-airplane')->action(function (TeamsWebhook $record, TeamsWebhookNotifier $notifier): void {
                $success = $notifier->sendTest($record);
                $notification = Notification::make()->title($success ? 'Test delivered' : 'Test delivery failed')->body($success ? 'Teams accepted the test notification.' : 'Check the last delivery error.');
                ($success ? $notification->success() : $notification->danger())->send();
            }),
            \Filament\Actions\EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTeamsWebhooks::route('/'), 'create' => Pages\CreateTeamsWebhook::route('/create'), 'edit' => Pages\EditTeamsWebhook::route('/{record}/edit')];
    }
}
