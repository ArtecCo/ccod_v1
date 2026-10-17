<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    public function infolist(Schema $schema): Schema
    {
        $sections = [
            Section::make('Role')
                ->schema([
                    TextEntry::make('name')->label('Role name'),
                    TextEntry::make('slug'),
                    TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('is_system_role')->label('System role')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                    TextEntry::make('is_active')->label('Active')->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                ])
                ->columns(2),
        ];

        $permissions = $this->record->permissions()->get()->keyBy('slug');

        foreach (collect(RolePermissionService::managedPermissions())->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $entries[] = TextEntry::make('permission_' . md5($slug))
                    ->label($definition['name'])
                    ->state($permissions->has($slug) ? 'Granted' : 'Not granted');
            }

            $sections[] = Section::make($category)
                ->schema($entries)
                ->columns(2);
        }

        return $schema->components($sections);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => RoleResource::canEdit($this->record)),
        ];
    }
}
