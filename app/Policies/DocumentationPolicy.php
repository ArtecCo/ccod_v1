<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Documentation;
use App\Models\User;

class DocumentationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Documentation $documentation): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->is_active && in_array($user->roleEnum(), [
            UserRole::GlobalOwner,
            UserRole::GlobalContributor,
        ], true);
    }

    public function update(User $user, Documentation $documentation): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Documentation $documentation): bool
    {
        return $this->create($user);
    }

    public function restore(User $user, Documentation $documentation): bool
    {
        return $this->create($user);
    }

    public function forceDelete(User $user, Documentation $documentation): bool
    {
        return $this->create($user);
    }
}
