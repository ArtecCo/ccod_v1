<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        app(RolePermissionService::class)->sync($this->record, $this->permissionIds($this->data));
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }

    private function permissionIds(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $key) => str_starts_with($key, 'permission_'))
            ->flatten()
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
