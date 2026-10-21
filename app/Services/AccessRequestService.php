<?php

namespace App\Services;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Models\AccessRequest;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Validation\ValidationException;

class AccessRequestService
{
    public function __construct(private readonly AccessAuthorizationService $authorization) {}

    public function createForUserGroup(
        User $user,
        UserGroup $group,
        string $reason,
        AccessRequestDuration $duration,
        ?\DateTimeInterface $requestedUntil = null,
    ): AccessRequest {
        if (! $this->authorization->canRequestUserGroup($user, $group)) {
            throw ValidationException::withMessages([
                'user_group' => 'You are not eligible to request access to this user group.',
            ]);
        }

        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) {
            throw ValidationException::withMessages([
                'requested_until' => 'A future expiry date is required for time-bound access.',
            ]);
        }

        if ($duration === AccessRequestDuration::Permanent) {
            $requestedUntil = null;
        }

        $pending = AccessRequest::query()
            ->where('user_id', $user->getKey())
            ->where('user_group_id', $group->getKey())
            ->where('target_type', AccessRequestTargetType::UserGroup->value)
            ->where('status', AccessRequestStatus::Pending->value)
            ->exists();

        if ($pending) {
            throw ValidationException::withMessages([
                'user_group' => 'You already have a pending access request for this user group.',
            ]);
        }

        return AccessRequest::create([
            'user_id' => $user->getKey(),
            'user_group_id' => $group->getKey(),
            'target_type' => AccessRequestTargetType::UserGroup,
            'target_name' => $group->name,
            'requested_role' => $group->role->slug,
            'reason' => $reason,
            'duration' => $duration,
            'requested_until' => $requestedUntil,
            'status' => AccessRequestStatus::Pending,
        ]);
    }

    public function approve(
        AccessRequest $request,
        User|Developer $actor,
        ?string $reason = null,
        ?AccessRequestDuration $duration = null,
        ?\DateTimeInterface $requestedUntil = null,
    ): void {
        $this->authorizeDecision($request, $actor);

        if ($request->status !== AccessRequestStatus::Pending) {
            throw ValidationException::withMessages([
                'request' => 'Only pending requests can be approved.',
            ]);
        }

        if ($request->target_type !== AccessRequestTargetType::UserGroup) {
            throw ValidationException::withMessages([
                'request' => 'Only User Group access requests are supported.',
            ]);
        }

        $group = $request->userGroup;
        if ($group === null) {
            throw ValidationException::withMessages([
                'request' => 'The requested user group no longer exists.',
            ]);
        }

        $duration ??= $request->duration;

        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) {
            throw ValidationException::withMessages([
                'requested_until' => 'A future expiry date is required for time-bound access.',
            ]);
        }

        if ($duration === AccessRequestDuration::Permanent) {
            $requestedUntil = null;
        }

        $group->users()->syncWithoutDetaching([
            $request->user_id => [
                'starts_at' => now(),
                'expires_at' => $duration === AccessRequestDuration::TimeBound ? $requestedUntil : null,
            ],
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
    }

    public function reject(AccessRequest $request, User|Developer $actor, ?string $reason = null): void
    {
        $this->authorizeDecision($request, $actor);

        if ($request->status !== AccessRequestStatus::Pending) {
            throw ValidationException::withMessages([
                'request' => 'Only pending requests can be rejected.',
            ]);
        }

        $request->forceFill([
            'status' => AccessRequestStatus::Rejected,
            'decided_by_type' => $actor instanceof User ? User::class : get_class($actor),
            'decided_by_id' => $actor->getKey(),
            'decided_at' => now(),
            'decision_reason' => $reason,
        ])->save();
    }

    private function authorizeDecision(AccessRequest $request, User|Developer $actor): void
    {
        if ($actor instanceof Developer) {
            if ($actor->is_active !== true) {
                abort(403);
            }
            return;
        }

        if (! $this->authorization->canApprove($actor, $request)) {
            abort(403);
        }
    }
}
