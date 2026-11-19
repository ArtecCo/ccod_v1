<?php

namespace App\Filament\Resources\TeamsWebhooks;

use App\Models\TeamsWebhook;
use App\Services\TeamsWebhookNotifier;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsWebhookResource extends Resource
{
    protected static ?string $model = TeamsWebhook::class;
    protected static ?string $navigationLabel = 'Webhook Destinations';
    protected static ?string $modelLabel = 'Webhook Destination';
    protected static ?string $pluralModelLabel = 'Webhook Destinations';
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
            Section::make('Webhook destination')
                ->description('Register the endpoint here. Choose its event, conditions and message template only when creating a Notification Rule.')
                ->schema([
                    TextInput::make('name')->required()->maxLength(120),
                    Textarea::make('description')->label('Description')->rows(2)->maxLength(1000),
                    Select::make('destination_type')
                        ->options([
                            'channel' => 'Microsoft Teams channel',
                            'chat' => 'Microsoft Teams chat',
                            'generic' => 'Generic HTTPS JSON endpoint',
                        ])
                        ->required()
                        ->live(),
                    Select::make('message_format')
                        ->label('Teams message format')
                        ->options([
                            'adaptive_card' => 'Adaptive Card (recommended)',
                            'message_card' => 'MessageCard (legacy)',
                        ])
                        ->default('adaptive_card')
                        ->required()
                        ->visible(fn (Get $get): bool => $get('destination_type') !== 'generic'),
                    TextInput::make('webhook_url')
                        ->label('Webhook URL')
                        ->url()
                        ->required()
                        ->password()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => trim((string) $state))
                        ->helperText('Stored encrypted. HTTPS endpoints only.'),
                    Toggle::make('enabled')->default(true)->label('Enabled'),
                    \Filament\Forms\Components\Hidden::make('category')->default('server_start')->dehydrated(false),
                    \Filament\Forms\Components\Hidden::make('message_template')->default('')->dehydrated(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('id', 'desc')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('description')->limit(70)->toggleable(),
            TextColumn::make('destination_type')->badge()->formatStateUsing(fn (string $state): string => match ($state) { 'channel' => 'Teams channel', 'chat' => 'Teams chat', 'generic' => 'Generic HTTPS', default => $state }),
            IconColumn::make('enabled')->boolean(),
            TextColumn::make('last_delivery_status')->badge()->placeholder('Not sent'),
            TextColumn::make('last_error')->label('Last error')->limit(60)->placeholder('—')->toggleable(),
            TextColumn::make('last_sent_at')->dateTime('Y-m-d H:i:s')->placeholder('Never'),
        ])->recordActions([
            Action::make('test')->label('Send test')->icon('heroicon-o-paper-airplane')->action(function (TeamsWebhook $record, TeamsWebhookNotifier $notifier): void {
                $success = $notifier->sendTest($record);
                $notification = Notification::make()->title($success ? 'Test delivered' : 'Test delivery failed')->body($success ? ($record->destination_type === 'generic' ? 'The webhook endpoint accepted the test notification.' : 'Teams accepted the test notification.') : 'Check the last delivery error.');
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
