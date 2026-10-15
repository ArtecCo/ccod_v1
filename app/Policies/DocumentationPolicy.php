<?php

namespace App\Policies;

use App\Models\Documentation;
use App\Models\User;
use App\Services\AccessAuthorizationService;

class DocumentationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active === true
            && app(AccessAuthorizationService::class)->hasPermission($user, 'documentation.view');
    }

    public function view(User $user, Documentation $documentation): bool
    {
        return $user->is_active === true
            && app(AccessAuthorizationService::class)->hasPermission($user, 'documentation.view');
    }

    public function create(User $user): bool
    {
        return $user->is_active === true
            && app(AccessAuthorizationService::class)->hasPermission($user, 'documentation.create');
    }

    public function update(User $user, Documentation $documentation): bool
    {
        return $user->is_active === true
            && app(AccessAuthorizationService::class)->hasPermission($user, 'documentation.edit');
    }

    public function delete(User $user, Documentation $documentation): bool
    {
        return $user->is_active === true
            && app(AccessAuthorizationService::class)->hasPermission($user, 'documentation.delete');
    }

    public function restore(User $user, Documentation $documentation): bool
    {
        return $this->delete($user, $documentation);
    }

    public function forceDelete(User $user, Documentation $documentation): bool
    {
        return $this->delete($user, $documentation);
    }
}
