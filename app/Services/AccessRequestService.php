<?php

namespace App\Services;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Notifications\ClientPortalNotification;
use Illuminate\Validation\ValidationException;

class AccessRequestService
{
    public function __construct(
        private readonly AccessAuthorizationService $authorization,
        private readonly NotificationService $notifications,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function create(
        User $user,
        AccessRequestTargetType $targetType,
        Team|AzureSubscription $target,
        UserRole $requestedRole,
        string $reason,
        AccessRequestDuration $duration,
        ?\DateTimeInterface $requestedUntil = null,
    ): AccessRequest {
        if ($requestedRole->isGlobal()) {
            throw ValidationException::withMessages(['requested_role' => 'Access requests can only grant a restricted role.']);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages(['user' => 'Inactive users cannot request access.']);
        }

        if (! $this->authorization->canRequestRole($user, $targetType, $target, $requestedRole)) {
            throw ValidationException::withMessages(['requested_role' => 'The requested role is not an upgrade over the current effective access.']);
        }

        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) {
            throw ValidationException::withMessages(['requested_until' => 'A future expiry date is required for time-bound access.']);
        }

        if ($duration === AccessRequestDuration::Permanent) {
            $requestedUntil = null;
        }

        $pending = AccessRequest::query()
            ->where('user_id', $user->getKey())
            ->where('target_type', $targetType->value)
            ->where('status', AccessRequestStatus::Pending->value)
            ->when(
                $targetType === AccessRequestTargetType::Team,
                fn ($query) => $query->where('team_id', $target->getKey()),
                fn ($query) => $query->where('subscription_id', $target->getKey()),
            )
            ->exists();

        if ($pending) {
            throw ValidationException::withMessages(['target' => 'You already have a pending access request for this target.']);
        }

        $request = AccessRequest::create([
            'user_id' => $user->getKey(),
            'target_type' => $targetType,
            'team_id' => $targetType === AccessRequestTargetType::Team ? $target->getKey() : null,
            'subscription_id' => $targetType === AccessRequestTargetType::Subscription ? $target->getKey() : null,
            'target_name' => $target instanceof Team ? $target->name : $target->display_name,
            'requested_role' => $requestedRole->value,
            'reason' => $reason,
            'duration' => $duration,
            'requested_until' => $requestedUntil,
            'status' => AccessRequestStatus::Pending,
        ]);

        $approvers = $this->authorization->approvers($request);

        foreach ($approvers as $approver) {
            $this->notifications->sendToUser(
                $approver,
                new ClientPortalNotification(
                    title: 'Access request pending',
                    message: sprintf('%s requested %s access to %s.', $user->name, $requestedRole->label(), $request->target_name),
                    type: 'access_request',
                    actionUrl: \App\Filament\Resources\AccessRequests\AccessRequestResource::getUrl('view', ['record' => $request]),
                    actionLabel: 'Review request',
                    severity: 'warning',
                ),
            );
        }

        $this->auditLogger->log(
            event: 'Access Request Created',
            description: sprintf('%s requested %s access to %s.', $user->name, $requestedRole->label(), $request->target_name),
            subject: $request,
            success: true,
            properties: [
                'target_type' => $targetType->value,
                'target_id' => $target->getKey(),
                'requested_role' => $requestedRole->value,
                'duration' => $duration->value,
                'approver_count' => $approvers->count(),
            ],
            logName: 'Access Requests',
            tags: ['access_request', 'created'],
            category: LogSettings::APPLICATION_REQUESTS,
        );

        return $request;
    }

    public function approve(
        AccessRequest $request,
        User|\App\Models\Developer $actor,
        ?string $reason = null,
        ?AccessRequestDuration $duration = null,
        ?\DateTimeInterface $requestedUntil = null,
    ): UserAccessGrant {
        if ($request->status !== AccessRequestStatus::Pending) {
            throw ValidationException::withMessages(['request' => 'Only pending requests can be approved.']);
        }

        if ($actor instanceof User && ! $this->authorization->canApprove($actor, $request)) {
            throw ValidationException::withMessages(['request' => 'You are not authorized to approve this request.']);
        }

        $role = UserRole::tryFrom($request->requested_role);

        if (! $role || $role->isGlobal()) {
            throw ValidationException::withMessages(['request' => 'The requested role is invalid.']);
        }

        $duration ??= $request->duration;

        if ($duration === AccessRequestDuration::TimeBound) {
            if ($requestedUntil === null || $requestedUntil <= now()) {
                throw ValidationException::withMessages(['requested_until' => 'A future expiry date is required for time-bound access.']);
            }
        } else {
            $requestedUntil = null;
        }

        $grant = UserAccessGrant::create([
            'user_id' => $request->user_id,
            'target_type' => $request->target_type,
            'team_id' => $request->team_id,
            'subscription_id' => $request->subscription_id,
            'target_name' => $request->target_name,
            'role' => $request->requested_role,
            'granted_by_type' => $actor instanceof User ? User::class : get_class($actor),
            'granted_by_id' => $actor->getKey(),
            'source_request_id' => $request->getKey(),
            'starts_at' => now(),
            'expires_at' => $duration === AccessRequestDuration::TimeBound ? $requestedUntil : null,
        ]);

        $request->forceFill([
            'status' => AccessRequestStatus::Approved,
            'duration' => $duration,
            'requested_until' => $requestedUntil,
            'decided_by_type' => $actor instanceof User ? User::class : get_class($actor),
            'decided_by_id' => $actor->getKey(),
            'decided_at' => now(),
            'decision_reason' => $reason,
        ])->save();

        $accessPeriod = $duration === AccessRequestDuration::Permanent
            ? 'permanent access'
            : sprintf('access until %s', $requestedUntil->format('Y-m-d H:i:s'));

        $this->notifyRequester(
            $request,
            'Access request approved',
            sprintf('Your request for %s access to %s was approved with %s.', $role->label(), $request->target_name, $accessPeriod),
            'success',
        );
        $this->auditDecision($request, $actor, true, $reason, $duration, $requestedUntil);

        return $grant;
    }

    public function reject(AccessRequest $request, User|\App\Models\Developer $actor, ?string $reason = null): void
    {
        if ($request->status !== AccessRequestStatus::Pending) {
            throw ValidationException::withMessages(['request' => 'Only pending requests can be rejected.']);
        }

        if ($actor instanceof User && ! $this->authorization->canApprove($actor, $request)) {
            throw ValidationException::withMessages(['request' => 'You are not authorized to reject this request.']);
        }

        $request->forceFill([
            'status' => AccessRequestStatus::Rejected,
            'decided_by_type' => $actor instanceof User ? User::class : get_class($actor),
            'decided_by_id' => $actor->getKey(),
            'decided_at' => now(),
            'decision_reason' => $reason,
        ])->save();

        $this->notifyRequester($request, 'Access request rejected', sprintf('Your request for access to %s was rejected.', $request->target_name), 'danger');
        $this->auditDecision($request, $actor, false, $reason);
    }

    private function notifyRequester(AccessRequest $request, string $title, string $message, string $severity): void
    {
        $user = $request->user;

        if (! $user?->is_active) {
            return;
        }

        $this->notifications->sendToUser(
            $user,
            new ClientPortalNotification(
                title: $title,
                message: $message,
                type: 'access_request',
                actionUrl: \App\Filament\Resources\AccessRequests\AccessRequestResource::getUrl('view', ['record' => $request]),
                actionLabel: 'View request',
                severity: $severity,
            ),
        );
    }

    private function auditDecision(
        AccessRequest $request,
        User|\App\Models\Developer $actor,
        bool $approved,
        ?string $reason,
        ?AccessRequestDuration $duration = null,
        ?\DateTimeInterface $requestedUntil = null,
    ): void {
        $this->auditLogger->log(
            event: $approved ? 'Access Request Approved' : 'Access Request Rejected',
            description: sprintf('%s was %s.', $request->getAuditLabel(), $approved ? 'approved' : 'rejected'),
            subject: $request,
            success: $approved,
            failureReason: $approved ? null : ($reason ?: 'Access request rejected.'),
            properties: [
                'requested_role' => $request->requested_role,
                'target_type' => $request->target_type->value,
                'target_id' => $request->team_id ?? $request->subscription_id,
                'approved_duration' => $duration?->value,
                'approved_until' => $requestedUntil?->format(DATE_ATOM),
            ],
            logName: 'Access Requests',
            tags: ['access_request', $approved ? 'approved' : 'rejected'],
            category: LogSettings::APPLICATION_REQUESTS,
        );
    }
}
