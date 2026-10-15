<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int> */
    protected array $legacyPermissionIds = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $permissions = $this->record->permissions()->get();

        $this->legacyPermissionIds = $permissions
            ->filter(fn ($permission): bool => str_starts_with($permission->slug, 'applications.') || str_starts_with($permission->slug, 'environments.') || $permission->slug === 'subscriptions.assign_environment')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        foreach ($permissions->groupBy('category') as $category => $categoryPermissions) {
            $visiblePermissions = $categoryPermissions->filter(function ($permission): bool {
                return match ($permission->category) {
                    'Projects & Applications' => str_starts_with($permission->slug, 'projects.'),
                    'Environments & Subscriptions' => str_starts_with($permission->slug, 'subscriptions.') && $permission->slug !== 'subscriptions.assign_environment',
                    default => true,
                };
            });

            if ($visiblePermissions->isNotEmpty()) {
                $data['permission_' . md5($category)] = $visiblePermissions->pluck('id')->all();
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(RolePermissionService::class)->sync(
            $this->record,
            array_merge($this->legacyPermissionIds, $this->permissionIds($this->data)),
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
