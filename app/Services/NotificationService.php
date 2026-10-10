<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Notifications\ClientPortalNotification;
use Illuminate\Support\Collection;
use Throwable;

class NotificationService
{
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
        array $teamIds = [],
        array $userIds = [],
        ?int $userId = null,
    ): int {
        $recipients = collect();

        if ($allUsers) {
            $recipients = $recipients->merge(User::query()->where('is_active', true)->get());
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

        $recipients = $recipients->unique(fn (User $user): int => $user->getKey())->values();

        $notification = new ClientPortalNotification(
            title: $title,
            message: $message,
            type: $type,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            severity: $severity,
        );

        $delivered = 0;

        try {
            $recipients->each(function (User $user) use ($notification, &$delivered): void {
                $user->notify($notification);
                $delivered++;
            });

            $this->auditLogger->log(
                event: 'Notification Sent',
                description: sprintf('Client portal notification "%s" delivered to %d active user(s).', $title, $delivered),
                success: true,
                properties: [
                    'type' => $type,
                    'severity' => $severity,
                    'recipient_count' => $delivered,
                    'all_users' => $allUsers,
                    'team_count' => count($teamIds),
                    'selected_user_count' => count($selectedUserIds),
                ],
                logName: 'Notifications',
                tags: ['notification', 'client_portal', 'sent'],
                category: LogSettings::NOTIFICATIONS,
            );
        } catch (Throwable $exception) {
            $this->auditLogger->log(
                event: 'Notification Send Failed',
                description: sprintf('Client portal notification "%s" could not be delivered completely.', $title),
                success: false,
                failureReason: $exception->getMessage(),
                properties: [
                    'type' => $type,
                    'severity' => $severity,
                    'recipient_count' => $recipients->count(),
                    'delivered_count' => $delivered,
                    'all_users' => $allUsers,
                    'team_count' => count($teamIds),
                    'selected_user_count' => count($selectedUserIds),
                ],
                logName: 'Notifications',
                tags: ['notification', 'client_portal', 'failure'],
                category: LogSettings::NOTIFICATIONS,
            );

            throw $exception;
        }

        return $delivered;
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
