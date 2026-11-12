<?php

namespace App\Filament\Resources\TeamsWebhooks;

use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookTemplate;
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
                        $template = TeamsWebhookTemplate::query()->where('category', $state)->where('is_default', true)->first()
                            ?? TeamsWebhookTemplate::query()->where('category', $state)->orderBy('id')->first();
                        $set('template_id', $template?->id);
                        $set('title_template', $template?->title_template ?? self::defaultTitleTemplate($state));
                        $set('message_template', $template?->message_template ?? self::defaultMessageTemplate($state));
                    }),
                Select::make('template_id')->label('Saved template')->options(fn ($get): array => TeamsWebhookTemplate::query()
                    ->when($get('category'), fn ($query, $category) => $query->where('category', $category))
                    ->orderByDesc('is_default')->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()->live()->afterStateUpdated(function (Set $set, $state): void {
                        $template = $state ? TeamsWebhookTemplate::find($state) : null;
                        if ($template) {
                            $set('title_template', $template->title_template);
                            $set('message_template', $template->message_template);
                        }
                    })->helperText('Choose a reusable template. You can still customize this webhook below.'),
                Select::make('destination_type')->options(['channel' => 'Teams channel', 'chat' => 'Teams chat'])->required()->live(),
                Select::make('message_format')->options(['adaptive_card' => 'Adaptive Card', 'message_card' => 'MessageCard (legacy)'])->default('adaptive_card')->required(),
                TextInput::make('webhook_url')->label('Webhook URL')->url()->required()->password()->revealable()->dehydrateStateUsing(fn ($state) => trim((string) $state))->helperText('Stored encrypted.'),
                Toggle::make('enabled')->default(true)->label('Enabled'),
            ])->columns(2),
            Section::make('Template selection')->description('Manage reusable templates under Developer → Notifications → Teams Message Templates. Changes to a saved template are used by every webhook linked to it.')->schema([
                Select::make('template_id')->label('Saved template')->options(fn ($get): array => TeamsWebhookTemplate::query()
                    ->when($get('category'), fn ($query, $category) => $query->where('category', $category))
                    ->orderByDesc('is_default')->orderBy('name')->pluck('name', 'id')->all())
                    ->default(fn ($get) => TeamsWebhookTemplate::query()->where('category', $get('category') ?? 'server_start')->where('is_default', true)->value('id'))
                    ->searchable()->required()->helperText('Templates are managed separately. Editing a template updates all webhooks using it.'),
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
            return "**Overall status:** {overall_status}\n**Started:** {sync_started_at}\n**Completed:** {completed_at}\n**Duration:** {duration}\n\n**Module status**\n- **Subscriptions:** {subscriptions_status}\n- **Resources:** {resources_status}\n- **Costs:** {costs_status}\n- **Security:** {security_status}\n- **Alerts:** {alerts_status}\n- **Web URLs:** {web_urls_status}";
        }

        return "**Server boot time:** {server_boot_time}\n\n**IIS**\n- **Status:** {iis_status}\n- **Start time:** {iis_started_at}\n\n**PHP FastCGI**\n- **Status:** {php_status}\n- **Start time:** {php_started_at}\n\n**MySQL2610**\n- **Status:** {mysql_status}\n- **Start time:** {mysql_started_at}\n\n**Laravel queue**\n- **Status:** {queue_status}\n- **Start time:** {queue_started_at}";
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
