<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function infolist(Schema $schema): Schema
    {
        $components = [
            Section::make('Role')
                ->schema([
                    TextEntry::make('name')->label('Role name'),
                    TextEntry::make('slug'),
                    TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('is_system_role')->label('System role')->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                    TextEntry::make('is_active')->label('Active')->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                ])
                ->columns(2),
        ];

        // Super Admin is an intrinsic full-access role. Its effective permissions
        // must not depend on whether the permission_role pivot has been repaired
        // or populated by a migration.
        $isSuperAdmin = $this->record->slug === 'super_admin';

        $permissionSlugs = $isSuperAdmin
            ? collect(array_keys(RolePermissionService::managedPermissions()))->flip()
            : $this->record->permissions()
                ->where('permissions.is_active', true)
                ->pluck('permissions.slug')
                ->map(static fn ($slug): string => (string) $slug)
                ->flip();

        foreach (collect(RolePermissionService::managedPermissions())->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $granted = $permissionSlugs->has($slug);

                $entries[] = TextEntry::make('permission_' . md5($slug))
                    ->label($definition['name'])
                    ->state($granted ? '✓ Granted' : '✕ Not granted')
                    ->color($granted ? 'success' : 'danger');
            }

            $components[] = Section::make($category)
                ->schema($entries)
                ->columns(2);
        }

        return $schema->components($components);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => RoleResource::canEdit($this->record)),
        ];
    }
}
