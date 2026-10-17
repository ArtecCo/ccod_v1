<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Role;
use App\Services\RolePermissionService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Auth\Access\AuthorizationException;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int> */
    protected array $preservedPermissionIds = [];

    /** @var array<int|string> */
    protected array $submittedPermissionValues = [];

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->submittedPermissionValues = $this->permissionValues($data);

        foreach (array_keys($this->permissionFieldKeys()) as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(RolePermissionService::class)->sync(
            $this->record,
            array_merge($this->preservedPermissionIds, $this->submittedPermissionValues),
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => RoleResource::canDelete($this->record))
                ->using(function (Role $record): bool {
                    if (! RoleResource::canDelete($record)) {
                        throw new AuthorizationException('You are not authorized to delete this role.');
                    }

                    $assignedUsers = $record->assignments()
                        ->where(function ($query): void {
                            $query
                                ->whereNull('starts_at')
                                ->orWhere('starts_at', '<=', now());
                        })
                        ->where(function ($query): void {
                            $query
                                ->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        })
                        ->with('user:id,name')
                        ->get()
                        ->pluck('user')
                        ->filter()
                        ->unique('id');

                    if ($assignedUsers->isNotEmpty()) {
                        $names = $assignedUsers
                            ->map(fn ($user): string => (string) $user->name)
                            ->values()
                            ->implode(', ');

                        throw new AuthorizationException(
                            'This role cannot be deleted because it is currently assigned to the following users: ' . $names . '. Remove the role assignment from these users before deleting the role.'
                        );
                    }

                    return (bool) $record->delete();
                }),
        ];
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
