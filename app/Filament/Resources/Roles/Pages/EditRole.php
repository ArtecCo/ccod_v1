<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int> */
    protected array $preservedPermissionIds = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $permissions = $this->record->permissions()->get();
        $managedSlugs = array_keys(RolePermissionService::managedPermissions());

        $this->preservedPermissionIds = $permissions
            ->reject(fn ($permission): bool => in_array($permission->slug, $managedSlugs, true))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        foreach ($permissions->whereIn('slug', $managedSlugs)->groupBy('category') as $category => $categoryPermissions) {
            $data['permission_' . md5($category)] = $categoryPermissions
                ->pluck('slug')
                ->map(fn ($slug): string => (string) $slug)
                ->all();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(RolePermissionService::class)->sync(
            $this->record,
            array_merge($this->preservedPermissionIds, $this->permissionSlugs($this->form->getState())),
        );
    }

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\DeleteAction::make()->disabled(fn () => $this->record->is_system_role)];
    }

    protected function getRedirectUrl(): string
    {
        return RoleResource::getUrl('index');
    }

    private function permissionSlugs(array $data): array
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
