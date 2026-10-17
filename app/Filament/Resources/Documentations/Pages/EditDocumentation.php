<?php

namespace App\Filament\Resources\Documentations\Pages;

use App\Filament\Resources\Documentations\DocumentationResource;
use App\Models\Documentation;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

class EditDocumentation extends EditRecord
{
    protected static string $resource = DocumentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => DocumentationResource::canDelete($this->record))
                ->using(function (Documentation $record): bool {
                    if (! DocumentationResource::canDelete($record)) {
                        throw new AuthorizationException('You are not authorized to delete this documentation.');
                    }

                    return (bool) $record->delete();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return DocumentationResource::getUrl('index');
    }
}
