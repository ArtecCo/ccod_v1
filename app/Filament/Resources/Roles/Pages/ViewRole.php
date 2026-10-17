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

        $managed = RolePermissionService::managedPermissions();
        $isSuperAdmin = $this->record->slug === 'super_admin';

        $grantedSlugs = $isSuperAdmin
            ? array_keys($managed)
            : $this->record->permissions()
                ->whereIn('permissions.slug', array_keys($managed))
                ->pluck('permissions.slug')
                ->map(static fn ($slug): string => (string) $slug)
                ->all();

        $grantedSlugs = array_fill_keys($grantedSlugs, true);

        foreach (collect($managed)->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $statePath = 'permission_status_' . md5($slug);
                $this->record->setAttribute(
                    $statePath,
                    isset($grantedSlugs[$slug]) ? '✓ Granted' : '✕ Not granted',
                );

                $entries[] = TextEntry::make($statePath)
                    ->label($definition['name'])
                    ->color(isset($grantedSlugs[$slug]) ? 'success' : 'danger');
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
