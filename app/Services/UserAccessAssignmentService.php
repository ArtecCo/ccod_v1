<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserAccessAssignmentService
{
    public function sync(User $user, array $rows): void
    {
        DB::transaction(function () use ($user, $rows): void {
            $existingAccesses = $user->resourceAccesses()->get()->keyBy('id');
            $existingAssignments = $user->roleAssignments()->get()->keyBy('id');

            $keptAccessIds = [];
            $keptAssignmentIds = [];

            foreach ($rows as $row) {
                $accessType = $row['access_type'] ?? null;
                $resourceId = $row['resource_id'] ?? null;

                if (! in_array($accessType, ['business_unit', 'project', 'subscription'], true) || blank($resourceId)) {
                    continue;
                }

                $attributes = [
                    'access_type' => $accessType,
                    'business_unit_id' => $accessType === 'business_unit' ? $resourceId : null,
                    'project_id' => $accessType === 'project' ? $resourceId : null,
                    'subscription_id' => $accessType === 'subscription' ? $resourceId : null,
                ];

                $accessId = isset($row['resource_access_id'])
                    ? (int) $row['resource_access_id']
                    : null;

                $access = $accessId && $existingAccesses->has($accessId)
                    ? $existingAccesses->get($accessId)
                    : $user->resourceAccesses()->make();

                $access->fill($attributes);
                $access->save();

                $keptAccessIds[] = $access->id;

                if (blank($row['role_id'] ?? null)) {
                    continue;
                }

                $assignmentId = isset($row['role_assignment_id'])
                    ? (int) $row['role_assignment_id']
                    : null;

                $assignment = $assignmentId && $existingAssignments->has($assignmentId)
                    ? $existingAssignments->get($assignmentId)
                    : $user->roleAssignments()->make();

                $assignment->fill([
                    'role_id' => $row['role_id'],
                    'scope_type' => $accessType,
                    'resource_access_id' => $access->id,
                    'starts_at' => $row['starts_at'] ?? null,
                    'expires_at' => $row['expires_at'] ?? null,
                ]);
                $assignment->save();

                $keptAssignmentIds[] = $assignment->id;
            }

            $user->roleAssignments()
                ->when($keptAssignmentIds !== [], fn ($query) => $query->whereNotIn('id', $keptAssignmentIds))
                ->delete();

            $user->resourceAccesses()
                ->when($keptAccessIds !== [], fn ($query) => $query->whereNotIn('id', $keptAccessIds))
                ->delete();
        });
    }
}
