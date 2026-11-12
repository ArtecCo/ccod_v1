<?php

namespace App\Filament\Resources\TeamsWebhookCategories;

use App\Models\TeamsWebhookCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsWebhookCategoryResource extends Resource
{
    protected static ?string $model = TeamsWebhookCategory::class;
    protected static ?string $navigationLabel = 'Template Categories';
    protected static ?string $modelLabel = 'Template Category';
    protected static ?string $pluralModelLabel = 'Template Categories';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifications';
    protected static ?int $navigationSort = 32;
    protected static ?string $slug = 'teams-template-categories';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Category')->schema([
                TextInput::make('name')->label('Category name')->required()->maxLength(80)
                    ->helperText('A unique name shown when configuring templates and webhooks.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('name')->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->copyable(),
            TextColumn::make('templates_count')->counts('templates')->label('Templates'),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make()->before(function (DeleteAction $action, TeamsWebhookCategory $record): void {
                if ($record->templates()->exists()) {
                    Notification::make()
                        ->title('Category cannot be deleted')
                        ->body('Delete or move its message templates before deleting this category.')
                        ->danger()
                        ->send();

                    $action->halt();
                }
            }),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamsWebhookCategories::route('/'),
            'create' => Pages\CreateTeamsWebhookCategory::route('/create'),
            'edit' => Pages\EditTeamsWebhookCategory::route('/{record}/edit'),
        ];
    }
}
