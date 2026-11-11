<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\UserGroups\UserGroupResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\FailedJob;
use App\Models\Project;
use App\Models\QueueJob;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DeveloperOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $database = DB::connection()->getDatabaseName();
        $tableCount = DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_TYPE', 'BASE TABLE')
            ->count();

        $auditCount = Activity::query()->count();
        $todayActivity = Activity::query()->where('created_at', '>=', now()->startOfDay())->count();

        return [
            Stat::make('Database Tables', number_format($tableCount))
                ->description('Base tables in the connected database')
                ->icon('heroicon-o-table-cells'),
            Stat::make('Subscriptions', number_format(AzureSubscription::query()->count()))
                ->description('Azure subscriptions registered')
                ->icon('heroicon-o-cloud')
                ->url(AzureSubscriptionResource::getUrl()),
            Stat::make('Users', number_format(User::query()->count()))
                ->description('Client portal accounts')
                ->icon('heroicon-o-users')
                ->url(UserResource::getUrl()),
            Stat::make('Developers', number_format(Developer::query()->count()))
                ->description('Developer panel accounts')
                ->icon('heroicon-o-code-bracket'),
            Stat::make('Projects', number_format(Project::query()->count()))
                ->description('Projects configured')
                ->icon('heroicon-o-rectangle-stack')
                ->url(ProjectResource::getUrl()),
            Stat::make('User Groups', number_format(UserGroup::query()->count()))
                ->description('Access groups configured')
                ->icon('heroicon-o-user-group')
                ->url(UserGroupResource::getUrl()),
            Stat::make('Business Units', number_format(BusinessUnit::query()->count()))
                ->description('Business units configured')
                ->icon('heroicon-o-building-office-2'),
            Stat::make('Roles', number_format(Role::query()->count()))
                ->description('Roles configured')
                ->icon('heroicon-o-identification'),
            Stat::make('Audit Log Entries', number_format($auditCount))
                ->description('Recorded events across the entire application')
                ->icon('heroicon-o-shield-check')
                ->url(AuditLogResource::getUrl()),
            Stat::make('Activity Today', number_format($todayActivity))
                ->description('Audit events recorded since midnight')
                ->icon('heroicon-o-bolt')
                ->url(AuditLogResource::getUrl()),
            Stat::make('Queued Jobs', number_format(QueueJob::query()->count()))
                ->description('Jobs waiting in the database queue')
                ->icon('heroicon-o-queue-list'),
            Stat::make('Failed Jobs', number_format(FailedJob::query()->count()))
                ->description('Jobs requiring developer attention')
                ->icon('heroicon-o-exclamation-triangle'),
        ];
    }
}
