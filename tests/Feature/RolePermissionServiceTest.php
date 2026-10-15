<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Tests\TestCase;

class RolePermissionServiceTest extends TestCase
{
    public function test_module_actions_are_removed_when_view_permission_is_absent(): void
    {
        $view = Permission::create([
            'name' => 'View cost data',
            'slug' => 'cost.view',
            'category' => 'Cost',
            'is_active' => true,
        ]);

        $export = Permission::create([
            'name' => 'Export cost data',
            'slug' => 'cost.export',
            'category' => 'Cost',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Test role without view',
            'slug' => 'test-role-without-view',
            'is_active' => true,
            'is_system_role' => false,
        ]);

        app(RolePermissionService::class)->sync($role, [$export->id]);

        $this->assertSame([], $role->fresh()->permissions()->pluck('id')->all());

        app(RolePermissionService::class)->sync($role, [$view->id, $export->id]);

        $this->assertEqualsCanonicalizing(
            [$view->id, $export->id],
            $role->fresh()->permissions()->pluck('id')->all(),
        );
    }

    public function test_monitoring_permissions_require_alerts_view(): void
    {
        $alertsView = Permission::create([
            'name' => 'View alerts',
            'slug' => 'alerts.view',
            'category' => 'Monitoring & Alerts',
            'is_active' => true,
        ]);

        $history = Permission::create([
            'name' => 'View monitoring history',
            'slug' => 'monitoring.history.view',
            'category' => 'Monitoring & Alerts',
            'is_active' => true,
        ]);

        $role = Role::create([
            'name' => 'Test monitoring role',
            'slug' => 'test-monitoring-role',
            'is_active' => true,
            'is_system_role' => false,
        ]);

        app(RolePermissionService::class)->sync($role, [$history->id]);
        $this->assertSame([], $role->fresh()->permissions()->pluck('id')->all());

        app(RolePermissionService::class)->sync($role, [$alertsView->id, $history->id]);
        $this->assertEqualsCanonicalizing(
            [$alertsView->id, $history->id],
            $role->fresh()->permissions()->pluck('id')->all(),
        );
    }
}
