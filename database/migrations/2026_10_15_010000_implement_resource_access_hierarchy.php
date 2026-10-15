<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('subscription_id');
            $table->string('environment', 20);
            $table->timestamps();
            $table->foreign('subscription_id')->references('subscription_id')->on('azure_subscriptions')->cascadeOnDelete();
            $table->unique(['project_id', 'environment']);
            $table->unique('subscription_id');
        });

        $applicationProjects = [];
        foreach (DB::table('applications')->orderBy('id')->get() as $application) {
            $parent = DB::table('projects')->where('id', $application->project_id)->first();
            $projectId = DB::table('projects')
                ->where('name', $application->name)
                ->where('business_unit_id', $parent?->business_unit_id)
                ->value('id');

            if ($projectId === null) {
                $projectId = DB::table('projects')->insertGetId([
                    'name' => $application->name,
                    'description' => $application->description,
                    'business_unit_id' => $parent?->business_unit_id,
                    'is_active' => $application->is_active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $applicationProjects[$application->id] = $projectId;
        }

        foreach (DB::table('azure_subscriptions')
            ->whereNotNull('application_id')
            ->whereNotNull('environment')
            ->get(['subscription_id', 'application_id', 'environment']) as $subscription) {
            $projectId = $applicationProjects[$subscription->application_id] ?? null;
            if ($projectId === null) continue;

            DB::table('project_subscriptions')->updateOrInsert(
                ['subscription_id' => $subscription->subscription_id],
                [
                    'project_id' => $projectId,
                    'environment' => strtolower($subscription->environment),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        Schema::create('user_resource_access', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('access_type');
            $table->foreignId('business_unit_id')->nullable()->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->string('subscription_id')->nullable();
            $table->timestamps();
            $table->foreign('subscription_id')->references('subscription_id')->on('azure_subscriptions')->cascadeOnDelete();
            $table->index(['user_id', 'access_type']);
            $table->index(['access_type', 'business_unit_id']);
            $table->index(['access_type', 'project_id']);
            $table->index(['access_type', 'subscription_id']);
        });

        Schema::table('user_role_assignments', function (Blueprint $table): void {
            $table->foreignId('resource_access_id')->nullable()->after('role_id')->constrained('user_resource_access')->cascadeOnDelete();
            $table->index(['user_id', 'resource_access_id']);
        });

        $resourceAccessId = function (int $userId, string $type, ?int $businessUnitId, ?int $projectId, ?string $subscriptionId): int {
            $query = DB::table('user_resource_access')->where('user_id', $userId)->where('access_type', $type);
            if ($type === 'business_unit') $query->where('business_unit_id', $businessUnitId);
            if ($type === 'project') $query->where('project_id', $projectId);
            if ($type === 'subscription') $query->where('subscription_id', $subscriptionId);
            $existing = $query->value('id');
            if ($existing !== null) return (int) $existing;

            return (int) DB::table('user_resource_access')->insertGetId([
                'user_id' => $userId,
                'access_type' => $type,
                'business_unit_id' => $businessUnitId,
                'project_id' => $projectId,
                'subscription_id' => $subscriptionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };

        foreach (DB::table('user_role_assignments')->get() as $assignment) {
            $type = null;
            $businessUnitId = null;
            $projectId = null;
            $subscriptionId = null;

            if ($assignment->scope_type === 'business_unit') {
                $type = 'business_unit';
                $businessUnitId = $assignment->business_unit_id;
            } elseif ($assignment->scope_type === 'project') {
                $type = 'project';
                $projectId = $assignment->project_id;
            } elseif ($assignment->scope_type === 'application') {
                $type = 'project';
                $projectId = $applicationProjects[$assignment->application_id] ?? null;
            } elseif ($assignment->scope_type === 'subscription') {
                $type = 'subscription';
                $subscriptionId = $assignment->subscription_id;
            } elseif ($assignment->scope_type === 'environment') {
                $projectId = $applicationProjects[$assignment->application_id] ?? $assignment->project_id;
                $subscriptionId = DB::table('project_subscriptions')
                    ->where('project_id', $projectId)
                    ->where('environment', strtolower((string) $assignment->environment))
                    ->value('subscription_id');
                if ($subscriptionId !== null) $type = 'subscription';
            }

            if ($type === null) continue;
            if ($type === 'business_unit' && $businessUnitId === null) continue;
            if ($type === 'project' && $projectId === null) continue;
            if ($type === 'subscription' && $subscriptionId === null) continue;

            DB::table('user_role_assignments')->where('id', $assignment->id)->update([
                'resource_access_id' => $resourceAccessId($assignment->user_id, $type, $businessUnitId, $projectId, $subscriptionId),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('user_role_assignments', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'resource_access_id']);
            $table->dropConstrainedForeignId('resource_access_id');
        });
        Schema::dropIfExists('user_resource_access');
        Schema::dropIfExists('project_subscriptions');
    }
};
