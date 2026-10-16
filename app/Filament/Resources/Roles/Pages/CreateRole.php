<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int|string> */
    protected array $submittedPermissionValues = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->submittedPermissionValues = $this->permissionValues($data);

        foreach (array_keys($this->permissionFieldKeys()) as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        app(RolePermissionService::class)->sync($this->record, $this->submittedPermissionValues);
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }

    /** @return array<int|string> */
    private function permissionValues(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'permission_'))
            ->flatten()
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->unique()
            ->values()
            ->all();
    }

    /** @return array<string, true> */
    private function permissionFieldKeys(): array
    {
        return collect(RolePermissionService::managedPermissions())
            ->groupBy('category')
            ->keys()
            ->mapWithKeys(fn (string $category): array => ['permission_' . md5($category) => true])
            ->all();
    }
}
