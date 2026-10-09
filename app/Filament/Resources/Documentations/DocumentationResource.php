<?php

namespace App\Filament\Resources\Documentations;

use App\Filament\Resources\Documentations\Pages\CreateDocumentation;
use App\Filament\Resources\Documentations\Pages\EditDocumentation;
use App\Filament\Resources\Documentations\Pages\ListDocumentations;
use App\Filament\Resources\Documentations\Pages\ViewDocumentation;
use App\Filament\Resources\Documentations\Schemas\DocumentationForm;
use App\Filament\Resources\Documentations\Tables\DocumentationsTable;
use App\Models\Documentation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DocumentationResource extends Resource
{
    protected static ?string $model = Documentation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Documentation';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    public static function canViewAny(): bool
    {
        return auth()->user()?->is_active ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('create', Documentation::class) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('update', $record) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('delete', $record) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return DocumentationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentations::route('/'),
            'create' => CreateDocumentation::route('/create'),
            'view' => ViewDocumentation::route('/{record}'),
            'edit' => EditDocumentation::route('/{record}/edit'),
        ];
    }
}
