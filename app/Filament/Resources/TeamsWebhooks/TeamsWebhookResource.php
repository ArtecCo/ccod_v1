<?php

namespace App\Filament\Resources\TeamsWebhooks;

use App\Models\TeamsWebhook;
use App\Services\TeamsWebhookNotifier;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
                Select::make('category')->options(['server_start' => 'Server start', 'sync_status' => 'Sync status'])->default('server_start')->required()->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('title_template', self::defaultTitleTemplate($state));
                        $set('message_template', self::defaultMessageTemplate($state));
                    }),
                Select::make('destination_type')->options(['channel' => 'Teams channel', 'chat' => 'Teams chat'])->required()->live(),
                Select::make('message_format')->options(['adaptive_card' => 'Adaptive Card', 'message_card' => 'MessageCard (legacy)'])->default('adaptive_card')->required()->visible(fn ($get): bool => $get('destination_type') === 'channel'),
                TextInput::make('webhook_url')->label('Webhook URL')->url()->required()->password()->revealable()->dehydrateStateUsing(fn ($state) => trim((string) $state))->helperText('Stored encrypted.'),
                Toggle::make('enabled')->default(true)->label('Enabled'),
            ])->columns(2),
            Section::make('Message template')->description('Use the category-specific placeholders listed below.')->schema([
                TextInput::make('title_template')->label('Title')->maxLength(200)->default(fn (): string => self::defaultTitleTemplate('server_start')),
                Textarea::make('message_template')->label('Message')->rows(12)->default(fn (): string => self::defaultMessageTemplate('server_start'))->required()
                    ->helperText(fn ($get): string => (
                        'This template is pre-filled. Values in braces are inserted automatically when the event occurs. '
                        .($get('destination_type') === 'chat' ? 'Chat: plain text with supported Markdown such as **bold**, *italic*, links, and line breaks. Font colors and arbitrary font sizes are not supported. ' : '')
                        .($get('category') === 'server_start'
                            ? 'Placeholders: {server_boot_time}, {iis_status}, {iis_started_at}, {php_status}, {php_started_at}, {mysql_status}, {mysql_started_at}, {queue_status}, {queue_started_at}'
                            : 'Placeholders: {overall_status}, {sync_started_at}, {completed_at}, {duration}, {subscriptions_status}, {resources_status}, {costs_status}, {security_status}, {alerts_status}, {web_urls_status}')
                    )),
            ]),
        ]);
    }

    public static function defaultTitleTemplate(?string $category): string
    {
        return $category === 'sync_status' ? 'CCOD Azure sync status' : 'CCOD server startup';
    }

    public static function defaultMessageTemplate(?string $category): string
    {
        if ($category === 'sync_status') {
            return "Overall status: {overall_status}\nStarted: {sync_started_at}\nCompleted: {completed_at}\nDuration: {duration}\n\nSubscriptions: {subscriptions_status}\nResources: {resources_status}\nCosts: {costs_status}\nSecurity: {security_status}\nAlerts: {alerts_status}\nWeb URLs: {web_urls_status}";
        }

        return "Windows boot time: {server_boot_time}\n\nIIS: {iis_status}\nIIS process started: {iis_started_at}\nPHP FastCGI: {php_status}\nPHP worker started: {php_started_at}\nMySQL2610: {mysql_status}\nMySQL process started: {mysql_started_at}\nLaravel queue: {queue_status}\nQueue worker started: {queue_started_at}";
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category')->badge()->formatStateUsing(fn (string $state): string => $state === 'server_start' ? 'Server start' : 'Sync status'),
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
