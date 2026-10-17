<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\RolePermissionService;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ViewEntry;
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
                    \Filament\Infolists\Components\TextEntry::make('name')->label('Role name'),
                    \Filament\Infolists\Components\TextEntry::make('slug'),
                    \Filament\Infolists\Components\TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                    \Filament\Infolists\Components\TextEntry::make('is_system_role')->label('System role')->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                    \Filament\Infolists\Components\TextEntry::make('is_active')->label('Active')->formatStateUsing(fn ($state): string => $state ? 'Yes' : 'No'),
                ])
                ->columns(2),
        ];

        $managed = RolePermissionService::managedPermissions();

        $grantedSlugs = DB::table('permission_role')
            ->join('permissions', 'permissions.id', '=', 'permission_role.permission_id')
            ->where('permission_role.role_id', $this->record->getKey())
            ->pluck('permissions.slug')
            ->map(static fn ($slug): string => (string) $slug)
            ->flip()
            ->all();

        if (
            $this->record->is_system_role &&
            (
                $this->record->slug === 'super_admin' ||
                strcasecmp(trim((string) $this->record->name), 'Super Admin') === 0
            )
        ) {
            $grantedSlugs = array_fill_keys(array_keys($managed), true);
        }

        foreach (collect($managed)->groupBy('category') as $category => $definitions) {
            $entries = [];

            foreach ($definitions as $slug => $definition) {
                $entries[] = ViewEntry::make('permission_status_' . md5($slug))
                    ->label($definition['name'])
                    ->view('filament.infolists.entries.permission-status')
                    ->viewData([
                        'granted' => isset($grantedSlugs[$slug]),
                    ]);
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
