<?php

namespace App\Services;

use App\Models\UserGroup;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use Illuminate\Support\Collection;
use Throwable;

class NotificationService
{
    private const RECIPIENT_CHUNK_SIZE = 250;

    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function send(
        string $title,
        string $message,
        string $type = 'general',
        string $severity = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        bool $allUsers = false,
        array $userGroupIds = [],
        array $userIds = [],
        ?int $userId = null,
    ): int {
        $selectedUserIds = collect($userIds)
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->when($userId !== null, fn (Collection $ids): Collection => $ids->push($userId))
            ->unique()
            ->values()
            ->all();

        $selectedUsers = $selectedUserIds !== []
            ? User::query()->whereKey($selectedUserIds)->get(['id', 'is_active'])
            : collect();

        $matchedSelectedUserIds = $selectedUsers
            ->filter(fn (User $user): bool => (bool) $user->is_active)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        $inactiveSelectedUserIds = $selectedUsers
            ->reject(fn (User $user): bool => (bool) $user->is_active)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();

        $recipientQuery = User::query()->where('is_active', true);

        if (! $allUsers && $userGroupIds !== [] && $selectedUserIds !== []) {
            $recipientQuery->where(function ($query) use ($userGroupIds, $selectedUserIds): void {
                $query->whereHas('userGroups', fn ($groupQuery) => $groupQuery->whereIn('user_groups.id', $userGroupIds))
                    ->orWhereIn('id', $selectedUserIds);
            });
        } elseif (! $allUsers && $userGroupIds !== []) {
            $recipientQuery->whereHas('userGroups', fn ($query) => $query->whereIn('user_groups.id', $userGroupIds));
        } elseif (! $allUsers && $selectedUserIds !== []) {
            $recipientQuery->whereIn('id', $selectedUserIds);
        } elseif (! $allUsers) {
            $recipientQuery->whereRaw('1 = 0');
        }

        $notification = new ClientPortalNotification(
            title: $title,
            message: $message,
            type: $type,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            severity: $severity,
        );

        $queued = 0;
        $recipientCount = 0;

        try {
            $recipientQuery
                ->select('users.*')
                ->orderBy('users.id')
                ->chunkById(self::RECIPIENT_CHUNK_SIZE, function (Collection $users) use ($notification, &$queued, &$recipientCount): void {
                    $recipientCount += $users->count();

                    foreach ($users as $user) {
                        $user->notify($notification);
                        $queued++;
                    }
                });

            $audience = $allUsers ? 'all' : ($userGroupIds !== [] && $selectedUserIds !== [] ? 'teams_and_users' : ($userGroupIds !== [] ? 'teams' : ($selectedUserIds !== [] ? 'users' : 'none')));

            $this->auditLogger->log(
                event: 'Notification Queued',
                description: sprintf(
                    'Client portal notification "%s": audience=%s; selected IDs=%s; found selected users=%d; active selected IDs=%s; inactive selected IDs=%s; matched active recipients=%d; queued=%d.',
                    $title,
                    $audience,
                    json_encode($selectedUserIds),
                    $selectedUsers->count(),
                    json_encode($matchedSelectedUserIds),
                    json_encode($inactiveSelectedUserIds),
                    $recipientCount,
                    $queued,
                ),
                success: true,
                properties: [
                    'type' => $type,
                    'severity' => $severity,
                    'recipient_count' => $queued,
                    'resolved_recipient_count' => $recipientCount,
                    'all_users' => $allUsers,
                    'user_group_count' => count($userGroupIds),
                    'selected_user_count' => count($selectedUserIds),
                    'selected_user_ids' => $selectedUserIds,
                    'found_selected_user_count' => $selectedUsers->count(),
                    'active_selected_user_ids' => $matchedSelectedUserIds,
                    'inactive_selected_user_ids' => $inactiveSelectedUserIds,
                ],
                logName: 'Notifications',
                tags: ['notification', 'client_portal', 'queued'],
                category: LogSettings::NOTIFICATIONS,
            );
        } catch (Throwable $exception) {
            $this->auditLogger->log(
                event: 'Notification Queue Failed',
                description: sprintf('Client portal notification "%s" could not be queued completely.', $title),
                success: false,
                failureReason: $exception->getMessage(),
                properties: [
                    'type' => $type,
                    'severity' => $severity,
                    'recipient_count' => $recipientCount,
                    'queued_count' => $queued,
                    'all_users' => $allUsers,
                    'user_group_count' => count($userGroupIds),
                    'selected_user_count' => count($selectedUserIds),
                ],
                logName: 'Notifications',
                tags: ['notification', 'client_portal', 'queue_failure'],
                category: LogSettings::NOTIFICATIONS,
            );

            throw $exception;
        }

        return $queued;
    }

    public function sendToUser(User $user, ClientPortalNotification $notification): void
    {
        if ($user->is_active) {
            $user->notify($notification);
        }
    }

    public function sendToUserGroup(UserGroup $userGroup, ClientPortalNotification $notification): int
    {
        $users = $userGroup->users()->where('is_active', true)->get();
        $users->each(fn (User $user): mixed => $user->notify($notification));

        return $users->count();
    }
}
