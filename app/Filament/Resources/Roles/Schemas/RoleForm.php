<?php

namespace App\Filament\Resources\Roles\Schemas;

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
                    TextInput::make('name')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function ($set, $get, ?string $state): void {
                        if (blank($get('slug'))) $set('slug', Str::slug((string) $state));
                    })->unique(table: Role::class, column: 'name', ignoreRecord: true),
                    TextInput::make('slug')->required()->alphaDash()->maxLength(255)->unique(table: Role::class, column: 'slug', ignoreRecord: true)->helperText('Automatically suggested from the role name. You can change it if needed.'),
                    Textarea::make('description')->columnSpan(1),
                    Toggle::make('is_active')->default(true)->columnSpan(1),
                    Toggle::make('is_system_role')->hidden()->dehydrated(false),
                ]),
        ];

        $permissionSections = [];
        $managed = collect(RolePermissionService::managedPermissions());
        $viewDependencies = RolePermissionService::viewPermissionForActions();

        foreach ($managed->groupBy('category') as $category => $definitions) {
            $categoryDefinitions = $managed->filter(fn (array $definition): bool => $definition['category'] === $category);
            $categorySlugs = $categoryDefinitions->keys()->values()->all();
            $options = $categoryDefinitions->mapWithKeys(fn (array $definition, string $slug): array => [$slug => $definition['name']])->all();
            $viewSlug = collect($categorySlugs)->first(fn (string $slug): bool => str_ends_with($slug, '.view'));
            $dependentActionSlugs = collect($viewDependencies)
                ->filter(fn (string $view): bool => $view === $viewSlug)
                ->keys()
                ->filter(fn (string $slug): bool => in_array($slug, $categorySlugs, true))
                ->values()
                ->all();
            $fieldName = 'permission_' . md5($category);

            $permissionSections[] = Section::make($category)->schema([
                CheckboxList::make($fieldName)
                    ->label('Actions')
                    ->options($options)
                    ->columns(2)
                    ->bulkToggleable()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($set, ?array $state) use ($fieldName, $viewSlug, $dependentActionSlugs): void {
                        if ($viewSlug === null || $dependentActionSlugs === []) return;
                        $state = array_map('strval', $state ?? []);
                        if (array_intersect($dependentActionSlugs, $state) !== [] && ! in_array($viewSlug, $state, true)) {
                            $state[] = $viewSlug;
                            $set($fieldName, array_values(array_unique($state)));
                        }
                    }),
            ]);
        }

        if ($permissionSections !== []) $sections[] = Grid::make(2)->schema($permissionSections)->columnSpanFull();
        return $schema->components($sections);
    }
}
