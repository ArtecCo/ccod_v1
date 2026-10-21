<?php

namespace App\Filament\Resources\UserGroups;

use App\Filament\Resources\UserGroups\Pages\CreateUserGroup;
use App\Filament\Resources\UserGroups\Pages\EditUserGroup;
use App\Filament\Resources\UserGroups\Pages\ListUserGroups;
use App\Filament\Resources\UserGroups\Pages\ViewUserGroup;
use App\Filament\Resources\UserGroups\Schemas\UserGroupForm;
use App\Filament\Resources\UserGroups\Schemas\UserGroupInfolist;
use App\Filament\Resources\UserGroups\Tables\UserGroupsTable;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserGroupResource extends Resource
{
    protected static ?string $model = UserGroup::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'User Groups';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool { return static::canManage(); }
    public static function canView($record): bool { return static::canManage(); }
    public static function canCreate(): bool { return static::canManage(); }
    public static function canEdit($record): bool { return static::canManage(); }
    public static function canDelete($record): bool { return static::canManage(); }

    public static function form(Schema $schema): Schema { return UserGroupForm::configure($schema); }
    public static function infolist(Schema $schema): Schema { return UserGroupInfolist::configure($schema); }
    public static function table(Table $table): Table { return UserGroupsTable::configure($table); }

    public static function getPages(): array
    {
        return [
            'index' => ListUserGroups::route('/'),
            'create' => CreateUserGroup::route('/create'),
            'view' => ViewUserGroup::route('/{record}'),
            'edit' => EditUserGroup::route('/{record}/edit'),
        ];
    }

    private static function canManage(): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User
            && $user->is_active
            && app(AccessAuthorizationService::class)->hasPermission($user, 'users.edit');
    }
}
