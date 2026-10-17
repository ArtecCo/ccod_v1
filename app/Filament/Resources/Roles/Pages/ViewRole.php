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

        $permissions = $this->record->permissions()->pluck('slug')->flip();

        foreach (collect(RolePermissionService::managedPermissions())->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $entries[] = IconEntry::make('permission_' . md5($slug))
                    ->label($definition['name'])
                    ->state($permissions->has($slug))
                    ->boolean();
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
