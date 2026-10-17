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

        $managedSlugs = array_keys(RolePermissionService::managedPermissions());

        // Use the same permission assignments that the role edit page uses.
        // The View page is only displaying what is assigned to the role; it should
        // not apply an additional active-state filter that can make granted entries
        // appear as not granted.
        $grantedSlugs = $this->record->permissions()
            ->whereIn('permissions.slug', $managedSlugs)
            ->pluck('permissions.slug')
            ->map(static fn ($slug): string => (string) $slug)
            ->flip()
            ->all();

        // Super Admin is an intrinsic full-access role. Treat it as having every
        // managed permission even if the pivot data is incomplete.
        if (
            $this->record->is_system_role &&
            (
                $this->record->slug === 'super_admin' ||
                strcasecmp(trim((string) $this->record->name), 'Super Admin') === 0
            )
        ) {
            $grantedSlugs = array_fill_keys($managedSlugs, true);
        }

        foreach (collect(RolePermissionService::managedPermissions())->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $granted = isset($grantedSlugs[$slug]);
                $stateKey = 'permission_' . md5($slug);

                $entries[] = TextEntry::make($stateKey)
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
