<?php

namespace App\Filament\Resources\Roles;

use App\Models\Role;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool { return static::canManage('roles.view'); }
    public static function canView($record): bool { return static::canManage('roles.view'); }
    public static function canCreate(): bool { return static::canManage('roles.create'); }
    public static function canEdit($record): bool { return static::canManage('roles.edit'); }
    public static function canDelete($record): bool { return static::canManage('roles.delete') && ! $record->is_system_role; }
    public static function form(Schema $schema): Schema { return \App\Filament\Resources\Roles\Schemas\RoleForm::configure($schema); }
    public static function table(Table $table): Table { return \App\Filament\Resources\Roles\Tables\RolesTable::configure($table); }
    public static function getPages(): array { return ['index' => \App\Filament\Resources\Roles\Pages\ListRoles::route('/'), 'create' => \App\Filament\Resources\Roles\Pages\CreateRole::route('/create'), 'view' => \App\Filament\Resources\Roles\Pages\ViewRole::route('/{record}'), 'edit' => \App\Filament\Resources\Roles\Pages\EditRole::route('/{record}/edit')]; }

    public static function canManage(string $permission = 'roles.view'): bool
    {
        $user = auth()->guard('web')->user();

        return ($user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, $permission))
            || auth()->guard('developers')->user()?->is_active === true;
    }
}
