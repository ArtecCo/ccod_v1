<?php
namespace App\Filament\Resources\AccessRequests;
use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\Role;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class AccessRequestResource extends Resource
{
 protected static ?string $model=AccessRequest::class;protected static ?string $navigationLabel='Access Requests';protected static string|\BackedEnum|null $navigationIcon='heroicon-o-key';protected static string|\UnitEnum|null $navigationGroup='Administration';
 public static function canViewAny():bool{return auth()->guard('web')->user()!==null||auth()->guard('developers')->user()?->is_active===true;}
 public static function shouldRegisterNavigation():bool{$u=auth()->guard('web')->user();return auth()->guard('developers')->user()?->is_active===true||($u instanceof User&&app(\App\Services\AccessAuthorizationService::class)->hasPermission($u,'users.roles.assign'));}
 public static function getEloquentQuery():Builder{$u=auth()->guard('web')->user();if(auth()->guard('developers')->user()?->is_active===true)return parent::getEloquentQuery()->with(['user','businessUnit','subscription']);if(!$u)return parent::getEloquentQuery()->whereKey(0);$a=app(\App\Services\AccessAuthorizationService::class);return $a->hasPermission($u,'users.roles.assign')?parent::getEloquentQuery()->with(['user','businessUnit','subscription']):parent::getEloquentQuery()->with(['user','businessUnit','subscription'])->where('user_id',$u->getKey());}
 public static function form(Schema $schema):Schema{return $schema->components([]);}
 public static function infolist(Schema $schema):Schema{return $schema->components([Section::make('Request')->columns(2)->schema([TextEntry::make('user.name')->label('Requester'),TextEntry::make('user.email')->label('Email'),TextEntry::make('target_name')->label('Target'),TextEntry::make('requested_role')->label('Requested role')->formatStateUsing(fn($state)=>Role::query()->where('slug',$state instanceof \BackedEnum?$state->value:$state)->value('name')??(string)$state),TextEntry::make('status')->badge(),TextEntry::make('reason')->columnSpanFull()])]);}
 public static function table(Table $table):Table{return $table->defaultSort('created_at','desc')->columns([TextColumn::make('user.name')->label('Requester')->searchable()->sortable(),TextColumn::make('target_name')->label('Target')->searchable()->sortable(),TextColumn::make('requested_role')->label('Requested role')->formatStateUsing(fn($state)=>Role::query()->where('slug',$state instanceof \BackedEnum?$state->value:$state)->value('name')??(string)$state),TextColumn::make('status')->badge(),TextColumn::make('created_at')->dateTime()->sortable()])->filters([SelectFilter::make('status')->options(collect(AccessRequestStatus::cases())->mapWithKeys(fn($s)=>[$s->value=>$s->label()])->all())])->actions([ViewAction::make()]);}
 public static function getPages():array{return ['index'=>ListAccessRequests::route('/'),'view'=>ViewAccessRequest::route('/{record}'),'create'=>\App\Filament\Resources\AccessRequests\Pages\CreateAccessRequest::route('/create')];}
}
