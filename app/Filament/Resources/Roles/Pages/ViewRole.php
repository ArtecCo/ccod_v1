<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function infolist(Schema $schema): Schema
    {
        $components = [
            Section::make('Role')
                ->description('Role details and status.')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Role name'),
                    TextEntry::make('slug'),
                    TextEntry::make('description')->placeholder('—')->columnSpan(1),
                    TextEntry::make('is_active')
                        ->label('Active')
                        ->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                    TextEntry::make('is_system_role')
                        ->label('System role')
                        ->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                ]),
        ];

        $managed = RolePermissionService::managedPermissions();
        $isSuperAdmin = $this->record->slug === 'super_admin';
        $permissionSections = [];

        foreach (collect($managed)->groupBy('category', preserveKeys: true) as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $granted = $isSuperAdmin || $this->record->permissions()
                    ->where('permissions.slug', $slug)
                    ->exists();

                $entries[] = TextEntry::make('permission_' . md5($slug))
                    ->label($definition['name'])
                    ->state($granted ? '✓ Granted' : '✕ Not granted')
                    ->color($granted ? 'success' : 'danger');
            }

            $permissionSections[] = Section::make($category)
                ->schema($entries)
                ->columns(2);
        }

        if ($permissionSections !== []) {
            $components[] = Grid::make(2)
                ->schema($permissionSections)
                ->columnSpanFull();
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
