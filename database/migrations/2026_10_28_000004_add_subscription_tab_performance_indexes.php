<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexIfMissing('azure_alerts', ['subscription_id', 'last_modified_time'], 'azure_alerts_subscription_modified_idx');
        $this->addIndexIfMissing('azure_alerts', ['subscription_id', 'severity'], 'azure_alerts_subscription_severity_idx');

        $this->addIndexIfMissing('azure_security_assessments', ['subscription_id', 'state', 'status_changed_at'], 'azure_security_subscription_state_changed_idx');
        $this->addIndexIfMissing('azure_security_assessments', ['subscription_id', 'state', 'severity'], 'azure_security_subscription_state_severity_idx');
        $this->addIndexIfMissing('azure_security_assessments', ['subscription_id', 'state', 'resource_id'], 'azure_security_subscription_state_resource_idx');

        $this->addIndexIfMissing('azure_subscription_costs', ['subscription_id', 'granularity', 'period_start'], 'azure_cost_subscription_history_idx');
        $this->addIndexIfMissing('billing_resources', ['subscription_id', 'name'], 'billing_resources_subscription_name_idx');
    }

    public function down(): void
    {
        $this->dropIndexIfPresent('azure_alerts', 'azure_alerts_subscription_modified_idx');
        $this->dropIndexIfPresent('azure_alerts', 'azure_alerts_subscription_severity_idx');
        $this->dropIndexIfPresent('azure_security_assessments', 'azure_security_subscription_state_changed_idx');
        $this->dropIndexIfPresent('azure_security_assessments', 'azure_security_subscription_state_severity_idx');
        $this->dropIndexIfPresent('azure_security_assessments', 'azure_security_subscription_state_resource_idx');
        $this->dropIndexIfPresent('azure_subscription_costs', 'azure_cost_subscription_history_idx');
        $this->dropIndexIfPresent('billing_resources', 'billing_resources_subscription_name_idx');
    }

    private function addIndexIfMissing(string $table, array $columns, string $index): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $index): void {
            $blueprint->index($columns, $index);
        });
    }

    private function dropIndexIfPresent(string $table, string $index): void
    {
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $index)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($index): void {
            $blueprint->dropIndex($index);
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return Schema::getConnection()->getSchemaBuilder()->hasIndex($table, $index);
    }
};
