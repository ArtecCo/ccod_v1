<?php

namespace App\Services;

use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\Project;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;

class MandatoryNotificationService
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function userAddedToGroup(UserGroup $group, array $userIds): void
    {
        $this->dispatchRule(\App\Enums\NotificationEvent::UserGroupMemberAdded, ['group_id' => $group->getKey(), 'group_name' => $group->name, 'user_ids' => $userIds]);
        $this->send(
            'You were added to a user group',
            sprintf('You have been added to the user group "%s".', $group->name),
            $userIds,
            'access',
        );
    }

    public function groupRoleChanged(UserGroup $group): void
    {
        $group->load('role');

        $this->dispatchRule(\App\Enums\NotificationEvent::UserGroupRoleChanged, ['group_id' => $group->getKey(), 'group_name' => $group->name, 'role_name' => $group->role?->name]);
        $this->send(
            'Your user group role changed',
            sprintf('The role for user group "%s" has been changed to %s.', $group->name, $group->role?->name ?? 'a new role'),
            $this->activeGroupMemberIds($group),
            'access',
        );
    }

    public function accessRequestSubmitted(AccessRequest $request): void
    {
        $request->loadMissing(['user', 'userGroup']);
        $approvers = app(AccessAuthorizationService::class)->approvers($request);

        $this->dispatchRule(\App\Enums\NotificationEvent::AccessRequestSubmitted, ['request_id' => $request->getKey(), 'user_id' => $request->user_id, 'user_name' => $request->user?->name, 'user_group_id' => $request->user_group_id, 'user_group_name' => $request->userGroup?->name]);
        $this->send(
            'Access request submitted',
            sprintf('%s has requested access to user group "%s".', $request->user?->name ?? 'A user', $request->userGroup?->name ?? $request->target_name),
            $approvers->pluck('id')->all(),
            'access',
            route('filament.admin.resources.access-requests.view', ['record' => $request->getKey()]),
            'View request',
        );
    }

    public function accessRequestDecided(AccessRequest $request, bool $accepted): void
    {
        $request->loadMissing('userGroup');

        $this->dispatchRule($accepted ? \App\Enums\NotificationEvent::AccessRequestAccepted : \App\Enums\NotificationEvent::AccessRequestRejected, ['request_id' => $request->getKey(), 'user_id' => $request->user_id, 'user_group_id' => $request->user_group_id, 'user_group_name' => $request->userGroup?->name]);
        $this->send(
            $accepted ? 'Access request accepted' : 'Access request rejected',
            $accepted
                ? sprintf('Your request to access user group "%s" was accepted.', $request->userGroup?->name ?? $request->target_name)
                : sprintf('Your request to access user group "%s" was rejected.', $request->userGroup?->name ?? $request->target_name),
            [(int) $request->user_id],
            'access',
            route('filament.admin.resources.access-requests.view', ['record' => $request->getKey()]),
            'View request',
        );
    }

    public function groupProjectChanged(UserGroup $group, Project $project, bool $added): void
    {
        $this->dispatchRule($added ? \App\Enums\NotificationEvent::ProjectAddedToGroup : \App\Enums\NotificationEvent::ProjectRemovedFromGroup, ['group_id' => $group->getKey(), 'group_name' => $group->name, 'project_id' => $project->getKey(), 'project_name' => $project->name]);
        $this->send(
            $added ? 'A project was added to your user group' : 'A project was removed from your user group',
            sprintf('Project "%s" was %s user group "%s".', $project->name, $added ? 'added to' : 'removed from', $group->name),
            $this->activeGroupMemberIds($group),
            'access',
        );
    }

    public function projectGroupChanged(UserGroup $group, Project $project, bool $added): void
    {
        $this->groupProjectChanged($group, $project, $added);
    }

    public function projectSubscriptionChanged(Project $project, string $subscriptionId, bool $assigned): void
    {
        // Resolve the name from the subscription record itself. On removal,
        // the project assignment row has already been deleted by this point.
        $subscriptionName = AzureSubscription::query()
            ->whereKey($subscriptionId)
            ->value('display_name') ?: $subscriptionId;

        $this->dispatchRule($assigned ? \App\Enums\NotificationEvent::ProjectSubscriptionAssigned : \App\Enums\NotificationEvent::ProjectSubscriptionRemoved, ['project_id' => $project->getKey(), 'project_name' => $project->name, 'subscription_id' => $subscriptionId, 'subscription_name' => $subscriptionName]);
        $this->send(
            $assigned ? 'A subscription was assigned to your project' : 'A subscription was removed from your project',
            sprintf('Azure subscription "%s" was %s project "%s".', $subscriptionName, $assigned ? 'assigned to' : 'removed from', $project->name),
            $this->activeProjectUserIds($project),
            'access',
        );
    }

    public function globalSubscriptionChanged(AzureSubscription $subscription, bool $added): void
    {
        $userIds = User::query()
            ->where('is_active', true)
            ->whereHas('userGroups', function (Builder $query): void {
                $query->whereHas('role', fn (Builder $role): Builder => $role
                    ->where('slug', 'super_admin')
                    ->where('is_active', true))
                    ->where(fn (Builder $membership): Builder => $membership
                        ->whereNull('user_group_user.starts_at')
                        ->orWhere('user_group_user.starts_at', '<=', now()))
                    ->where(fn (Builder $membership): Builder => $membership
                        ->whereNull('user_group_user.expires_at')
                        ->orWhere('user_group_user.expires_at', '>', now()));
            })
            ->pluck('id')
            ->all();

        $this->send(
            $added ? 'Azure subscription added' : 'Azure subscription removed',
            sprintf('Azure subscription "%s" was %s globally.', $subscription->display_name ?: $subscription->subscription_id, $added ? 'added' : 'removed'),
            $userIds,
            'azure',
        );
    }

    public function synchronizationFailed(array $context = []): void
    {
        $context['occurred_at'] = $context['occurred_at'] ?? now()->toIso8601String();
        $this->dispatchRule(\App\Enums\NotificationEvent::SynchronizationFailed, $context);
        $this->notifications->send(
            title: 'Azure synchronization failed',
            message: 'An Azure synchronization operation has failed. Please review the synchronization report.',
            type: 'azure',
            severity: 'warning',
            actionUrl: route('sync-report.latest'),
            actionLabel: 'View Sync Report',
            allUsers: true,
        );
    }

    private function dispatchRule(\App\Enums\NotificationEvent $event, array $context): void
    {
        try {
            app(NotificationRuleService::class)->trigger($event, $context);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function activeGroupMemberIds(UserGroup $group): array
    {
        return $group->users()
            ->where('users.is_active', true)
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('user_group_user.starts_at')
                ->orWhere('user_group_user.starts_at', '<=', now()))
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('user_group_user.expires_at')
                ->orWhere('user_group_user.expires_at', '>', now()))
            ->pluck('users.id')
            ->all();
    }

    private function activeProjectUserIds(Project $project): array
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('userGroups', function (Builder $query) use ($project): void {
                $query->whereHas('role', fn (Builder $role): Builder => $role->where('is_active', true))
                    ->where(function (Builder $groups) use ($project): void {
                        $groups->whereHas('projects', fn (Builder $projects): Builder => $projects->whereKey($project->getKey()))
                            ->orWhereHas('role', fn (Builder $role): Builder => $role->where('slug', 'super_admin'));
                    })
                    ->where(fn (Builder $membership): Builder => $membership
                        ->whereNull('user_group_user.starts_at')
                        ->orWhere('user_group_user.starts_at', '<=', now()))
                    ->where(fn (Builder $membership): Builder => $membership
                        ->whereNull('user_group_user.expires_at')
                        ->orWhere('user_group_user.expires_at', '>', now()));
            })
            ->pluck('users.id')
            ->all();
    }

    private function send(
        string $title,
        string $message,
        array $userIds,
        string $type,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
    ): void
    {
        $userIds = collect($userIds)->filter()->unique()->values()->all();

        if ($userIds === []) {
            return;
        }

        $this->notifications->send(
            title: $title,
            message: $message,
            type: $type,
            severity: 'info',
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            userIds: $userIds,
        );
    }
}
