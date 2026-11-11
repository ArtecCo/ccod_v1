<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserGroup;
use App\Services\AccessAuthorizationService;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Collection;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        abort_unless(auth()->guard('web')->user() instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }
    }

    public function content(Schema $schema): Schema
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $groups = $user->userGroups()
            ->with(['role', 'projects'])
            ->where(function ($query): void {
                $query->whereNull('user_group_user.starts_at')
                    ->orWhere('user_group_user.starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('user_group_user.expires_at')
                    ->orWhere('user_group_user.expires_at', '>', now());
            })
            ->whereHas('role', fn ($query) => $query->where('is_active', true))
            ->orderBy('name')
            ->get();

        $lastLogin = Activity::query()
            ->where('event', 'Login')
            ->where('causer_type', $user->getMorphClass())
            ->where('causer_id', $user->getKey())
            ->orderByDesc('created_at')
            ->skip(1)
            ->first()?->created_at;

        $sessionStartedAt = (int) session()->get('auth_session_started_at', now()->timestamp);
        $elapsedSeconds = max(0, now()->timestamp - $sessionStartedAt);
        $sessionDuration = sprintf('%02d:%02d:%02d', intdiv($elapsedSeconds, 3600), intdiv($elapsedSeconds % 3600, 60), $elapsedSeconds % 60);

        $projectCount = $groups
            ->flatMap(fn (UserGroup $group): Collection => $group->projects)
            ->unique('id')
            ->count();

        $subscriptionCount = app(AccessAuthorizationService::class)
            ->accessibleSubscriptions($user)
            ->count();

        $recentActivity = Activity::query()
            ->where('causer_type', $user->getMorphClass())
            ->where('causer_id', $user->getKey())
            ->where('log_name', '!=', 'Requests')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');

        $groupRows = $groups->isEmpty()
            ? [TextEntry::make('no_groups')->hiddenLabel()->state('You are not currently assigned to any active user groups.')->columnSpanFull()]
            : $groups->map(fn (UserGroup $group): Grid => Grid::make(2)
                ->schema([
                    TextEntry::make('group_name_'.$group->id)
                        ->label('User group')
                        ->weight('medium')
                        ->state($group->name),
                    TextEntry::make('group_role_'.$group->id)
                        ->label('Role')
                        ->badge()
                        ->color('info')
                        ->state($group->role?->name ?? '—'),
                ]))
                ->all();

        $activityEntries = $recentActivity->isEmpty()
            ? [TextEntry::make('no_recent_activity')->hiddenLabel()->state('No recent activity to display.')->columnSpanFull()]
            : $recentActivity->map(fn (Activity $activity): TextEntry => TextEntry::make('activity_'.$activity->getKey())
                ->label($activity->created_at?->format('d M Y, H:i') ?? 'Activity')
                ->state(trim(($activity->event ?: $activity->log_name).' — '.($activity->description ?? '')))
                ->columnSpanFull())
                ->all();

        return $schema->components([
            Section::make($greeting.', '.$user->name)
                ->description('Welcome back to CCOD. Your workspace and account details are ready.')
                ->icon('heroicon-o-sparkles')
                ->columnSpanFull(),
            Section::make('Account overview')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 3])->schema([
                        TextEntry::make('dashboard_email')->label('Email address')->icon('heroicon-o-envelope')->state($user->email),
                        TextEntry::make('dashboard_created')->label('Account created')->icon('heroicon-o-calendar-days')->state($user->created_at?->format('d M Y') ?? '—'),
                        TextEntry::make('dashboard_status')->label('Account status')->badge()->color($user->is_active ? 'success' : 'danger')->state($user->is_active ? 'Active' : 'Inactive'),
                        TextEntry::make('dashboard_last_login')->label('Previous login')->icon('heroicon-o-clock')->state($lastLogin?->format('d M Y, H:i') ?? 'No previous login recorded'),
                        TextEntry::make('dashboard_session')->label('Current session')->icon('heroicon-o-clock')->state($sessionDuration),
                        TextEntry::make('dashboard_group_count')->label('Active user groups')->icon('heroicon-o-user-group')->state((string) $groups->count()),
                    ]),
                ])
                ->extraAttributes(['wire:poll.1s' => '$refresh'])
                ->columnSpanFull(),
            Section::make('My workspace')
                ->description('Your current access and the resources available to you.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('dashboard_groups')->label('User groups')->state((string) $groups->count())->icon('heroicon-o-user-group'),
                        TextEntry::make('dashboard_projects')->label('Assigned projects')->state((string) $projectCount)->icon('heroicon-o-rectangle-stack'),
                        TextEntry::make('dashboard_subscriptions')->label('Accessible subscriptions')->state((string) $subscriptionCount)->icon('heroicon-o-cloud'),
                    ]),
                ])
                ->columnSpanFull(),
            Section::make('My user groups')
                ->description('Your active group memberships and assigned roles.')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2])->schema($groupRows),
                ])
                ->columnSpanFull(),
            Section::make('Recent activity')
                ->description('Your latest recorded account and workspace activity.')
                ->schema($activityEntries)
                ->collapsible()
                ->columnSpanFull(),
        ]);
    }

    public function getWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('myProfile')
                ->label('My Profile')
                ->icon('heroicon-o-user-circle')
                ->url(fn (): string => Profile::getUrl())
                ->button(),
            Action::make('notifications')
                ->label('Notifications')
                ->icon('heroicon-o-bell')
                ->url(fn (): string => ClientNotifications::getUrl())
                ->button(),
        ];
    }
}
