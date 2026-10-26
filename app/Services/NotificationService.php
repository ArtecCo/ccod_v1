<?php

namespace App\Services;

use App\Models\Team;
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
        array $teamIds = [],
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

        $recipientQuery = User::query()->where('is_active', true);

        if (! $allUsers && $teamIds !== [] && $selectedUserIds !== []) {
            $recipientQuery->where(function ($query) use ($teamIds, $selectedUserIds): void {
                $query->whereHas('teams', fn ($teamQuery) => $teamQuery->whereIn('teams.id', $teamIds))
                    ->orWhereIn('id', $selectedUserIds);
            });
        } elseif (! $allUsers && $teamIds !== []) {
            $recipientQuery->whereHas('teams', fn ($query) => $query->whereIn('teams.id', $teamIds));
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

            $this->auditLogger->log(
                event: 'Notification Queued',
                description: sprintf('Client portal notification "%s" queued for %d active user(s).', $title, $queued),
                success: true,
                properties: [
                    'type' => $type,
                    'severity' => $severity,
                    'recipient_count' => $queued,
                    'resolved_recipient_count' => $recipientCount,
                    'all_users' => $allUsers,
                    'team_count' => count($teamIds),
                    'selected_user_count' => count($selectedUserIds),
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
                    'team_count' => count($teamIds),
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

    public function sendToTeam(Team $team, ClientPortalNotification $notification): int
    {
        $users = $team->users()->where('is_active', true)->get();
        $users->each(fn (User $user): mixed => $user->notify($notification));

        return $users->count();
    }
}
