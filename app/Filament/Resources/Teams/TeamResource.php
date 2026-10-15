<?php
namespace App\Filament\Resources\Teams;
use App\Filament\Resources\Teams\Pages\CreateTeam;
use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Filament\Resources\Teams\Schemas\TeamForm;
use App\Filament\Resources\Teams\Schemas\TeamInfolist;
use App\Filament\Resources\Teams\Tables\TeamsTable;
use App\Models\BusinessUnit;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
class TeamResource extends Resource
{
 protected static ?string $model=BusinessUnit::class;protected static string|BackedEnum|null $navigationIcon=Heroicon::OutlinedUserGroup;protected static ?string $navigationLabel='Business Units';protected static string|\UnitEnum|null $navigationGroup='Administration';
 public static function canViewAny():bool{return static::canManage('users.scopes.assign');}public static function canCreate():bool{return static::canManage('subscriptions.assign_business_unit');}public static function canEdit($record):bool{return static::canManage('subscriptions.assign_business_unit');}public static function canDelete($record):bool{return static::canManage('subscriptions.assign_business_unit');}
 public static function form(Schema $schema):Schema{return TeamForm::configure($schema);}public static function infolist(Schema $schema):Schema{return TeamInfolist::configure($schema);}public static function table(Table $table):Table{return TeamsTable::configure($table);}public static function getPages():array{return ['index'=>ListTeams::route('/'),'create'=>CreateTeam::route('/create'),'view'=>ViewTeam::route('/{record}'),'edit'=>EditTeam::route('/{record}/edit')];}
 private static function canManage(string $permission):bool{$u=auth()->guard('web')->user();return $u instanceof User&&app(\App\Services\AccessAuthorizationService::class)->hasPermission($u,$permission);}
}
