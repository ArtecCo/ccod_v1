<?php

namespace App\Filament\Resources\TeamsWebhookTemplates;

use App\Models\TeamsWebhookTemplate;
use App\Models\TeamsWebhookCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsWebhookTemplateResource extends Resource
{
    protected static ?string $model = TeamsWebhookTemplate::class;
    protected static ?string $navigationLabel = 'Teams Message Templates';
    protected static ?string $modelLabel = 'Message Template';
    protected static ?string $pluralModelLabel = 'Message Templates';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifications';
    protected static ?int $navigationSort = 31;
    protected static ?string $slug = 'teams-message-templates';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template')->schema([
                TextInput::make('name')->required()->maxLength(120),
                Select::make('category')->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'slug')->all())->searchable()->required(),
                Toggle::make('is_default')->label('Default template for this category')->helperText('Only one template per category can be the default.'),
                TextInput::make('title_template')->label('Title')->maxLength(200),
                Textarea::make('message_template')->label('Message')->rows(14)->required()
                    ->helperText('Use {field_name} placeholders from the event payload. Common fields: {event_key}, {event_label}, {is_test}, {test_timestamp}. Server: {server_boot_time}, {iis_status}, {iis_started_at}, {php_status}, {php_started_at}, {mysql_status}, {mysql_started_at}, {queue_status}, {queue_started_at}. Sync: {overall_status}, {sync_started_at}, {completed_at}, {duration}, {subscriptions_status}, {resources_status}, {costs_status}, {security_status}, {alerts_status}, {web_urls_status}. Other application events expose the context passed by their event producer. Unknown placeholders render as Not available.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('category')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category')->badge()->formatStateUsing(fn (string $state): string => TeamsWebhookCategory::query()->where('slug', $state)->value('name') ?? $state),
            IconColumn::make('is_default')->label('Default')->boolean(),
            TextColumn::make('updated_at')->label('Last updated')->dateTime('Y-m-d H:i:s')->sortable(),
        ])->filters([
            SelectFilter::make('category')->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'slug')->all()),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamsWebhookTemplates::route('/'),
            'create' => Pages\CreateTeamsWebhookTemplate::route('/create'),
            'edit' => Pages\EditTeamsWebhookTemplate::route('/{record}/edit'),
        ];
    }
}
