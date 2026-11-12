<?php

namespace App\Filament\Resources\TeamsWebhooks;

use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookTemplate;
use App\Models\TeamsWebhookCategory;
use App\Services\TeamsWebhookNotifier;
use App\Support\TeamsWebhookEvents;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\KeyValue;
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
                        ->options(TeamsWebhookEvents::options())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(fn (Get $get): array => TeamsWebhookEvents::defaultsForCategory($get('category')))
                        ->helperText('A destination can subscribe to multiple events.'),
                    Select::make('condition_mode')
                        ->label('Condition matching')
                        ->options(['all' => 'All conditions must match', 'any' => 'Any condition may match'])
                        ->default('all')
                        ->required(),
                    Repeater::make('conditions')
                        ->label('Optional event conditions')
                        ->schema([
                            TextInput::make('field')->label('Context field')->required()->maxLength(120),
                            Select::make('operator')->label('Operator')->options([
                                'equals' => 'Equals',
                                'not_equals' => 'Does not equal',
                                'contains' => 'Contains',
                                'not_contains' => 'Does not contain',
                                'greater_than' => 'Greater than',
                                'greater_or_equal' => 'Greater than or equal',
                                'less_than' => 'Less than',
                                'less_or_equal' => 'Less than or equal',
                                'in' => 'Is one of (comma separated)',
                                'exists' => 'Exists',
                                'not_exists' => 'Does not exist',
                            ])->default('equals')->required(),
                            TextInput::make('value')->label('Comparison value')->maxLength(500)
                                ->required(fn (Get $get): bool => ! in_array($get('operator'), ['exists', 'not_exists'], true))
                                ->visible(fn (Get $get): bool => ! in_array($get('operator'), ['exists', 'not_exists'], true)),
                        ])
                        ->columns(3)
                        ->addActionLabel('Add condition')
                        ->reorderable()
                        ->defaultItems(0)
                        ->collapsible()
                        ->helperText('Conditions can filter by event context fields such as overall_status, subscription_id, severity or cost. Leave empty to receive every selected event.'),
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
            TextColumn::make('event_keys')->label('Subscribed events')->formatStateUsing(fn ($state): string => collect($state ?? [])->map(fn ($key) => TeamsWebhookEvents::options()[$key] ?? $key)->implode(', '))->wrap(),
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
