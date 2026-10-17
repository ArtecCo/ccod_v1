<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $sections = [
            Section::make('Role')
                ->description('Create a custom role by defining its name and permissions. Custom roles are separate from built-in system roles.')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($set, $get, ?string $state): void {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug((string) $state));
                            }
                        })
                        ->unique(table: Role::class, column: 'name', ignoreRecord: true),
                    TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->maxLength(255)
                        ->unique(table: Role::class, column: 'slug', ignoreRecord: true)
                        ->helperText('Automatically suggested from the role name. You can change it if needed.'),
                    Textarea::make('description')->columnSpan(1),
                    Toggle::make('is_active')->default(true)->columnSpan(1),
                    Toggle::make('is_system_role')->hidden()->dehydrated(false),
                ]),
        ];

        $permissionSections = [];
        $managed = collect(RolePermissionService::managedPermissions());
        $viewDependencies = RolePermissionService::viewPermissionForActions();

        foreach ($managed->groupBy('category') as $category => $definitions) {
            // Only permissions belonging to this category are shown in this section.
            $categorySlugs = $definitions->keys()->all();

            $permissions = Permission::query()
                ->whereIn('slug', $categorySlugs)
                ->where('is_active', true)
                ->orderBy('id')
                ->get(['id', 'slug', 'name']);

            $options = $permissions
                ->mapWithKeys(fn (Permission $permission): array => [(string) $permission->id => $permission->name])
                ->all();

            $permissionIdsBySlug = $permissions
                ->mapWithKeys(fn (Permission $permission): array => [$permission->slug => (string) $permission->id])
                ->all();

            $viewSlug = $permissions
                ->first(fn (Permission $permission): bool => str_ends_with($permission->slug, '.view'))
                ?->slug;

            $viewId = $viewSlug !== null ? ($permissionIdsBySlug[$viewSlug] ?? null) : null;

            // Every action explicitly mapped to this category's View permission
            // automatically requires View when selected.
            $dependentActionIds = collect($viewDependencies)
                ->filter(fn (string $view): bool => $view === $viewSlug)
                ->keys()
                ->map(fn (string $slug): ?string => $permissionIdsBySlug[$slug] ?? null)
                ->filter()
                ->values()
                ->all();

            $fieldName = 'permission_' . md5($category);

            $permissionSections[] = Section::make($category)
                ->schema([
                    CheckboxList::make($fieldName)
                        ->label('Actions')
                        ->options($options)
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function ($set, ?array $state) use ($fieldName, $viewId, $dependentActionIds): void {
                            if ($viewId === null || $dependentActionIds === []) {
                                return;
                            }

                            $state = array_map('strval', $state ?? []);

                            if (array_intersect($dependentActionIds, $state) !== [] && ! in_array($viewId, $state, true)) {
                                $state[] = $viewId;
                                $set($fieldName, array_values(array_unique($state)));
                            }
                        })
                        ->disableOptionWhen(function (string $value, $get) use ($fieldName, $viewId, $dependentActionIds): bool {
                            if ($viewId === null || $value !== $viewId || $dependentActionIds === []) {
                                return false;
                            }

                            $state = array_map('strval', $get($fieldName) ?? []);

                            return array_intersect($dependentActionIds, $state) !== [];
                        }),
                ]);
        }

        if ($permissionSections !== []) {
            $sections[] = Grid::make(2)
                ->schema($permissionSections)
                ->columnSpanFull();
        }

        return $schema->components($sections);
    }
}
