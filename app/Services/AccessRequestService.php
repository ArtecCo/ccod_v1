<?php

namespace App\Services;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Models\UserGroup;
use App\Models\UserResourceAccess;
use App\Models\UserRoleAssignment;
use App\Models\UserSubscriptionAccessOverride;
use Illuminate\Validation\ValidationException;

class AccessRequestService
{
    public function __construct(private readonly AccessAuthorizationService $authorization, private readonly NotificationService $notifications, private readonly AuditLogger $auditLogger) {}

    public function create(User $user, AccessRequestTargetType $targetType, BusinessUnit|AzureSubscription $target, Role $requestedRole, string $reason, AccessRequestDuration $duration, ?\DateTimeInterface $requestedUntil = null): AccessRequest
    {
        if (! $user->is_active) throw ValidationException::withMessages(['user' => 'Inactive users cannot request access.']);
        if (! $this->authorization->canRequestRole($user, $targetType, $target, $requestedRole)) throw ValidationException::withMessages(['requested_role' => 'The requested role is not an upgrade over the current effective access.']);
        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) throw ValidationException::withMessages(['requested_until' => 'A future expiry date is required for time-bound access.']);
        if ($duration === AccessRequestDuration::Permanent) $requestedUntil = null;
        $pending = AccessRequest::query()->where('user_id', $user->getKey())->where('target_type', $targetType->value)->where('status', AccessRequestStatus::Pending->value)->when($targetType === AccessRequestTargetType::Team, fn ($q) => $q->where('business_unit_id', $target->getKey()), fn ($q) => $q->where('subscription_id', $target->getKey()))->exists();
        if ($pending) throw ValidationException::withMessages(['target' => 'You already have a pending access request for this target.']);
        return AccessRequest::create(['user_id' => $user->getKey(), 'target_type' => $targetType, 'business_unit_id' => $targetType === AccessRequestTargetType::Team ? $target->getKey() : null, 'subscription_id' => $targetType === AccessRequestTargetType::Subscription ? $target->getKey() : null, 'target_name' => $target instanceof BusinessUnit ? $target->name : $target->display_name, 'requested_role' => $requestedRole->slug, 'reason' => $reason, 'duration' => $duration, 'requested_until' => $requestedUntil, 'status' => AccessRequestStatus::Pending]);
    }

    public function createForUserGroup(User $user, UserGroup $group, string $reason, AccessRequestDuration $duration, ?\DateTimeInterface $requestedUntil = null): AccessRequest
    {
        if (! $this->authorization->canRequestUserGroup($user, $group)) throw ValidationException::withMessages(['user_group' => 'You are not eligible to request access to this user group.']);
        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) throw ValidationException::withMessages(['requested_until' => 'A future expiry date is required for time-bound access.']);
        if ($duration === AccessRequestDuration::Permanent) $requestedUntil = null;

        $pending = AccessRequest::query()->where('user_id', $user->getKey())->where('user_group_id', $group->getKey())->where('target_type', AccessRequestTargetType::UserGroup->value)->where('status', AccessRequestStatus::Pending->value)->exists();
        if ($pending) throw ValidationException::withMessages(['user_group' => 'You already have a pending access request for this user group.']);

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

    public function approve(AccessRequest $request, User|Developer $actor, ?string $reason = null, ?AccessRequestDuration $duration = null, ?\DateTimeInterface $requestedUntil = null): ?UserAccessGrant
    {
        $this->authorizeDecision($request, $actor);
        if ($request->status !== AccessRequestStatus::Pending) throw ValidationException::withMessages(['request' => 'Only pending requests can be approved.']);
        $duration ??= $request->duration;
        if ($duration === AccessRequestDuration::TimeBound && ($requestedUntil === null || $requestedUntil <= now())) throw ValidationException::withMessages(['requested_until' => 'A future expiry date is required for time-bound access.']);
        if ($duration === AccessRequestDuration::Permanent) $requestedUntil = null;

        if ($request->target_type === AccessRequestTargetType::UserGroup) {
            $group = $request->userGroup;
            if (! $group) throw ValidationException::withMessages(['request' => 'The requested user group no longer exists.']);
            $group->users()->syncWithoutDetaching([$request->user_id => ['starts_at' => now(), 'expires_at' => $duration === AccessRequestDuration::TimeBound ? $requestedUntil : null]]);
            $request->forceFill(['status' => AccessRequestStatus::Approved, 'duration' => $duration, 'requested_until' => $requestedUntil, 'decided_by_type' => $actor instanceof User ? User::class : get_class($actor), 'decided_by_id' => $actor->getKey(), 'decided_at' => now(), 'decision_reason' => $reason])->save();
            return null;
        }

        $role = Role::query()->where('slug', $request->requested_role)->where('is_active', true)->first();
        if (! $role) throw ValidationException::withMessages(['request' => 'The requested role is invalid.']);
        if ($request->target_type === AccessRequestTargetType::Subscription) UserSubscriptionAccessOverride::query()->where('user_id', $request->user_id)->where('subscription_id', $request->subscription_id)->delete();
        $resourceAccess = $this->resourceAccessForRequest($request);
        UserRoleAssignment::create(['user_id' => $request->user_id, 'role_id' => $role->getKey(), 'scope_type' => $request->target_type === AccessRequestTargetType::Team ? 'business_unit' : 'subscription', 'resource_access_id' => $resourceAccess->getKey(), 'granted_by_user_id' => $actor instanceof User ? $actor->getKey() : null, 'starts_at' => now(), 'expires_at' => $duration === AccessRequestDuration::TimeBound ? $requestedUntil : null]);
        $grant = UserAccessGrant::create(['user_id' => $request->user_id, 'target_type' => $request->target_type, 'business_unit_id' => $request->business_unit_id, 'subscription_id' => $request->subscription_id, 'target_name' => $request->target_name, 'role' => $role->slug, 'granted_by_type' => $actor instanceof User ? User::class : get_class($actor), 'granted_by_id' => $actor->getKey(), 'source_request_id' => $request->getKey(), 'starts_at' => now(), 'expires_at' => $duration === AccessRequestDuration::TimeBound ? $requestedUntil : null]);
        $request->forceFill(['status' => AccessRequestStatus::Approved, 'duration' => $duration, 'requested_until' => $requestedUntil, 'decided_by_type' => $actor instanceof User ? User::class : get_class($actor), 'decided_by_id' => $actor->getKey(), 'decided_at' => now(), 'decision_reason' => $reason])->save();
        return $grant;
    }

    public function reject(AccessRequest $request, User|Developer $actor, ?string $reason = null): void
    {
        $this->authorizeDecision($request, $actor);
        if ($request->status !== AccessRequestStatus::Pending) throw ValidationException::withMessages(['request' => 'Only pending requests can be rejected.']);
        $request->forceFill(['status' => AccessRequestStatus::Rejected, 'decided_by_type' => $actor instanceof User ? User::class : get_class($actor), 'decided_by_id' => $actor->getKey(), 'decided_at' => now(), 'decision_reason' => $reason])->save();
    }

    private function authorizeDecision(AccessRequest $request, User|Developer $actor): void
    {
        if ($actor instanceof Developer) { if ($actor->is_active !== true) abort(403); return; }
        if (! $this->authorization->canApprove($actor, $request)) abort(403);
    }

    private function resourceAccessForRequest(AccessRequest $request): UserResourceAccess
    {
        if ($request->target_type === AccessRequestTargetType::Team) return UserResourceAccess::firstOrCreate(['user_id' => $request->user_id, 'access_type' => 'business_unit', 'business_unit_id' => $request->business_unit_id, 'project_id' => null, 'subscription_id' => null]);
        return UserResourceAccess::firstOrCreate(['user_id' => $request->user_id, 'access_type' => 'subscription', 'business_unit_id' => null, 'project_id' => null, 'subscription_id' => $request->subscription_id]);
    }

    public function revokeSubscriptionAccess(User $targetUser, AzureSubscription $subscription, User|Developer $actor, ?string $reason = null): void
    {
        if (! $this->authorization->canRevokeSubscriptionAccess($actor, $targetUser, $subscription)) throw ValidationException::withMessages(['subscription' => 'You are not authorized to revoke this user\'s subscription access.']);
        UserSubscriptionAccessOverride::updateOrCreate(['user_id' => $targetUser->getKey(), 'subscription_id' => $subscription->getKey()], ['override' => 'revoked', 'revoked_by_type' => $actor instanceof User ? User::class : get_class($actor), 'revoked_by_id' => $actor->getKey(), 'revoked_at' => now(), 'reason' => $reason]);
    }
}
