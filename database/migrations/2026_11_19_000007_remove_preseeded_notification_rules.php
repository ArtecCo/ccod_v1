<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notification_rules')
            ->whereIn('event_key', [
                'azure_subscription_connected',
                'azure_subscription_disconnected',
                'azure_sync_succeeded',
                'azure_sync_failed',
                'azure_critical_alert_detected',
                'servicenow_incident_created',
                'servicenow_incident_resolved',
                'user_added_to_team',
                'user_removed_from_team',
                'subscription_assigned_to_team',
                'subscription_removed_from_team',
                'maintenance_scheduled',
                'maintenance_completed',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Intentionally do not recreate pre-seeded rules.
    }
};
