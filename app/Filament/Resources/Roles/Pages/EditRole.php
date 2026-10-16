<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()->disabled(fn () => $this->record->is_system_role)];
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }
}
