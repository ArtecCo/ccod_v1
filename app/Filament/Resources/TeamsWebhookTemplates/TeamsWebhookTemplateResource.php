<?php

namespace App\Filament\Resources\TeamsWebhookTemplates;

use App\Models\TeamsWebhookTemplate;
use App\Models\TeamsWebhookCategory;
use App\Support\TeamsWebhookEvents;
use App\Support\NotificationEventCatalog;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                Select::make('category')
                    ->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'slug')->all())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set): void {
                        $set('event_key', TeamsWebhookEvents::defaultsForCategory($state)[0] ?? null);
                        $set('placeholder_to_insert', null);
                    }),
                Select::make('event_key')
                    ->label('Event schema for this template')
                    ->options(TeamsWebhookEvents::options())
                    ->searchable()
                    ->required()
                    ->live()
                    ->helperText('Select the event whose payload fields should be available as message placeholders.'),
                Select::make('placeholder_to_insert')
                    ->label('Insert event field')
                    ->placeholder('Choose a field to insert into the message')
                    ->options(fn (Get $get): array => NotificationEventCatalog::placeholderOptions([$get('event_key')]))
                    ->searchable()
                    ->dehydrated(false)
                    ->live()
                    ->afterStateUpdated(function ($state, Get $get, Set $set): void {
                        if (! is_string($state) || $state === '') {
                            return;
                        }

                        $message = (string) ($get('message_template') ?? '');
                        $set('message_template', rtrim($message) . ($message !== '' ? "\n" : '') . $state);
                        $set('placeholder_to_insert', null);
                    })
                    ->helperText('Selecting a field appends its placeholder to the message below.'),
                Toggle::make('is_default')->label('Default template for this category')->helperText('Only one template per category can be the default.'),
                TextInput::make('title_template')->label('Title')->maxLength(200),
                Textarea::make('message_template')->label('Message')->rows(14)->required()
                    ->helperText('Use the Insert event field selector above to add valid placeholders. Fields are defined by the selected event schema. Unknown placeholders render as Not available.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('category')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('event_key')->label('Event')->formatStateUsing(fn (?string $state): string => $state ? (TeamsWebhookEvents::options()[$state] ?? $state) : 'Not assigned')->wrap(),
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
