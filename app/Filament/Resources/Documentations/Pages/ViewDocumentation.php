<?php

namespace App\Filament\Resources\Documentations\Pages;

use App\Filament\Resources\Documentations\DocumentationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\TextEntry;

class ViewDocumentation extends ViewRecord
{
    protected static string $resource = DocumentationResource::class;

    public function getTitle(): string
    {
        return $this->record->title;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            TextEntry::make('title')
                ->label('')
                ->size('xl')
                ->weight('bold'),
            TextEntry::make('author.name')
                ->label('Author'),
            TextEntry::make('created_at')
                ->label('Published')
                ->dateTime(),
            TextEntry::make('content')
                ->label('')
                ->html()
                ->columnSpanFull(),
        ];
    }
}
