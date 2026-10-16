<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Tests\TestCase;

class RolePermissionServiceTest extends TestCase
{
    public function test_action_permission_automatically_grants_view_permission(): void
    {
        $view = Permission::create([
            'name' => 'View projects',
            'slug' => 'projects.view',
            'category' => 'Projects',
            'is_active' => true,
        ]);

        $edit = Permission::create([
            'name' => 'Edit projects',
            'slug' => 'projects.edit',
            'category' => 'Projects',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Test project role',
            'slug' => 'test-project-role',
            'is_active' => true,
            'is_system_role' => false,
        ]);

        app(RolePermissionService::class)->sync($role, [$edit->id]);

        $this->assertEqualsCanonicalizing(
            [$view->id, $edit->id],
            $role->fresh()->permissions()->pluck('id')->all(),
        );
    }

    public function test_export_permission_automatically_grants_view_permission(): void
    {
        $view = Permission::create([
            'name' => 'View alerts',
            'slug' => 'alerts.view',
            'category' => 'Alerts',
            'is_active' => true,
        ]);

        $export = Permission::create([
            'name' => 'Export alerts',
            'slug' => 'alerts.export',
            'category' => 'Alerts',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Test alert role',
            'slug' => 'test-alert-role',
            'is_active' => true,
            'is_system_role' => false,
        ]);

        app(RolePermissionService::class)->sync($role, [$export->id]);

        $this->assertEqualsCanonicalizing(
            [$view->id, $export->id],
            $role->fresh()->permissions()->pluck('id')->all(),
        );
    }

    public function test_unmanaged_permissions_cannot_be_added_to_custom_roles(): void
    {
        $legacy = Permission::create([
            'name' => 'Legacy cost export',
            'slug' => 'cost.export',
            'category' => 'Cost',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Test clean role',
            'slug' => 'test-clean-role',
            'is_active' => true,
            'is_system_role' => false,
        ]);

        app(RolePermissionService::class)->sync($role, [$legacy->id]);

        $this->assertSame([], $role->fresh()->permissions()->pluck('id')->all());
    }
}
