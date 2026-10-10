<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send a client-portal database notification to a resolved audience.
     *
     * Supported audiences:
     * - all active users
     * - every member of one or more teams
     * - selected user IDs
     * - one individual user
     *
     * Recipients are de-duplicated by user ID so a user who belongs to multiple
     * selected teams receives the notification only once.
     */
    public function send(
        string $title,
        string $message,
        string $type = 'general',
        string $severity = 'info',
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        bool $allUsers = false,
        array $teamIds = [],
        array $userIds = [],
        ?int $userId = null,
    ): int {
        $recipients = collect();

        if ($allUsers) {
            $recipients = $recipients->merge(
                User::query()->where('is_active', true)->get(),
            );
        }

        if ($teamIds !== []) {
            $recipients = $recipients->merge(
                User::query()
                    ->where('is_active', true)
                    ->whereHas('teams', fn ($query) => $query->whereIn('teams.id', $teamIds))
                    ->get(),
            );
        }

        $selectedUserIds = collect($userIds)
            ->filter(fn ($id): bool => is_numeric($id))
            ->map(fn ($id): int => (int) $id)
            ->when($userId !== null, fn (Collection $ids): Collection => $ids->push($userId))
            ->unique()
            ->values()
            ->all();

        if ($selectedUserIds !== []) {
            $recipients = $recipients->merge(
                User::query()
                    ->where('is_active', true)
                    ->whereIn('id', $selectedUserIds)
                    ->get(),
            );
        }

        $notification = new ClientPortalNotification(
            title: $title,
            message: $message,
            type: $type,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            severity: $severity,
        );

        $recipients
            ->unique(fn (User $user): int => $user->getKey())
            ->each(fn (User $user): mixed => $user->notify($notification));

        return $recipients->unique(fn (User $user): int => $user->getKey())->count();
    }

    public function sendToUser(User $user, ClientPortalNotification $notification): void
    {
        if ($user->is_active) {
            $user->notify($notification);
        }
    }

    public function sendToTeam(Team $team, ClientPortalNotification $notification): int
    {
        $users = $team->users()->where('is_active', true)->get();

        $users->each(fn (User $user): mixed => $user->notify($notification));

        return $users->count();
    }
}
