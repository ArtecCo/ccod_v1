<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('event_key')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('recipient_type');
            $table->json('recipient_ids')->nullable();
            $table->string('title', 150)->nullable();
            $table->text('message')->nullable();
            $table->string('type')->default('general');
            $table->string('severity')->default('info');
            $table->string('action_label', 80)->nullable();
            $table->string('action_url', 2048)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('developers')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('developers')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();

        DB::table('notification_rules')->insert(array_map(
            static fn (string $event): array => [
                'event_key' => $event,
                'enabled' => false,
                'recipient_type' => 'all',
                'recipient_ids' => null,
                'type' => 'general',
                'severity' => 'info',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
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
            ],
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_rules');
    }
};
