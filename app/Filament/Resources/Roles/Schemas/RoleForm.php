<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use App\Models\Role;
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

        foreach (
            Permission::query()
                ->where('is_active', true)
                ->where('category', '!=', 'Administration')
                ->whereNotIn('slug', [
                    'projects.view',
                    'applications.view',
                    'environments.view',
                    'subscriptions.view',
                ])
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category') as $category => $permissions
        ) {
            $displayPermissions = match ($category) {
                'Projects & Applications' => $permissions->filter(fn (Permission $permission): bool => str_starts_with($permission->slug, 'projects.')),
                'Environments & Subscriptions' => $permissions->filter(fn (Permission $permission): bool => str_starts_with($permission->slug, 'subscriptions.') && $permission->slug !== 'subscriptions.assign_environment'),
                default => $permissions,
            };

            if ($displayPermissions->isEmpty()) {
                continue;
            }

            $field = 'permission_' . md5($category);
            $categoryLabel = match ($category) {
                'Projects & Applications' => 'Projects',
                'Environments & Subscriptions' => 'Environments',
                default => $category,
            };

            $permissionSections[] = Section::make($categoryLabel)
                ->schema([
                    CheckboxList::make($field)
                        ->label('Actions')
                        ->options($displayPermissions->mapWithKeys(function (Permission $permission): array {
                            $label = match ($permission->slug) {
                                'subscriptions.view' => 'View environments',
                                'subscriptions.create' => 'Create environments',
                                'subscriptions.edit' => 'Edit environments',
                                'subscriptions.delete' => 'Delete environments',
                                'subscriptions.assign_business_unit' => 'Assign environments to business units',
                                default => $permission->name,
                            };

                            return [$permission->id => $label];
                        })->all())
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable()
                        ->live(),
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
