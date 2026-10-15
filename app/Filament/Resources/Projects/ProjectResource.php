<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProject;
use App\Filament\Resources\Projects\Schemas\ProjectForm;
use App\Filament\Resources\Projects\Schemas\ProjectInfolist;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Projects';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool { $user = auth()->guard('web')->user(); return $user instanceof User && app(\\App\\Services\\AccessAuthorizationService::class)->hasAnyResourceAccess($user); }
    public static function canCreate(): bool { return static::canManage('projects.create'); }
    public static function canView($record): bool { $user = auth()->guard('web')->user(); return $user instanceof User && app(\\App\\Services\\AccessAuthorizationService::class)->hasResourceAccess($user, $record); }
    public static function canEdit($record): bool { return static::canManage('projects.edit', $record); }
    public static function canDelete($record): bool { return static::canManage('projects.delete', $record); }
    public static function form(Schema $schema): Schema { return ProjectForm::configure($schema); }
    public static function infolist(Schema $schema): Schema { return ProjectInfolist::configure($schema); }
    public static function table(Table $table): Table { return ProjectsTable::configure($table); }
    public static function getPages(): array { return ['index'=>ListProjects::route('/'),'create'=>CreateProject::route('/create'),'view'=>ViewProject::route('/{record}'),'edit'=>EditProject::route('/{record}/edit')]; }

    private static function canManage(string $permission, ?Project $target = null): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) return true;
        $user = auth()->guard('web')->user();
        return $user instanceof User && app(\App\Services\AccessAuthorizationService::class)->hasPermission($user, $permission, $target);
    }
}
