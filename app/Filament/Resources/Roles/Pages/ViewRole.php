<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

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
                    IconEntry::make('is_system_role')->label('System role')->boolean(),
                    IconEntry::make('is_active')->label('Active')->boolean(),
                ])
                ->columns(2),
        ];

        // Read the authoritative role-permission mapping directly from the RBAC pivot.
        // These entries are dynamically generated and are not model-backed infolist fields.
        $permissionSlugs = DB::table('permission_role')
            ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
            ->where('permission_role.role_id', $this->record->getKey())
            ->pluck('permissions.slug')
            ->all();

        foreach (collect(RolePermissionService::managedPermissions())->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $hasPermission = in_array($slug, $permissionSlugs, true);

                $entries[] = IconEntry::make('permission_' . md5($slug))
                    ->label($definition['name'])
                    ->state($hasPermission ? 'granted' : 'denied')
                    ->icon($hasPermission ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color($hasPermission ? 'success' : 'danger');
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
