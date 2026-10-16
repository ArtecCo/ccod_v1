<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\Developer;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static ?string $navigationLabel = 'Users';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool { return static::canManage('users.view'); }
    public static function canView($record): bool { return static::canManage('users.view'); }
    public static function canCreate(): bool { return static::canManage('users.create'); }
    public static function canEdit($record): bool { return static::canManage('users.edit'); }
    public static function canDelete($record): bool { return static::canManage('users.delete'); }
    public static function form(Schema $schema): Schema { return UserForm::configure($schema); }
    public static function infolist(Schema $schema): Schema { return UserInfolist::configure($schema); }
    public static function table(Table $table): Table { return UsersTable::configure($table); }
    public static function getPages(): array { return ['index' => ListUsers::route('/'), 'create' => CreateUser::route('/create'), 'view' => ViewUser::route('/{record}'), 'edit' => EditUser::route('/{record}/edit')]; }

    private static function canManage(string $permission): bool
    {
        if (auth()->guard('developers')->user() instanceof Developer) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, $permission);
    }
}
