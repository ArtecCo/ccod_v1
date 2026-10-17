<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Role;
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

        $managed = RolePermissionService::managedPermissions();

        foreach (collect($managed)->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $entries[] = TextEntry::make('id')
                    ->label($definition['name'])
                    ->state(function (Role $record) use ($slug): string {
                        if ($record->slug === 'super_admin') {
                            return '✓ Granted';
                        }

                        return $record->permissions()
                            ->where('permissions.slug', $slug)
                            ->exists()
                            ? '✓ Granted'
                            : '✕ Not granted';
                    })
                    ->color(function (Role $record) use ($slug): string {
                        if ($record->slug === 'super_admin') {
                            return 'success';
                        }

                        return $record->permissions()
                            ->where('permissions.slug', $slug)
                            ->exists()
                            ? 'success'
                            : 'danger';
                    });
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
