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
            $table->string('subscription_id', 100);
            $table->string('environment', 20);
            $table->timestamps();

            $table->unique(['project_id', 'environment'], 'project_subscriptions_project_environment_unique');
            $table->unique('subscription_id', 'project_subscriptions_subscription_unique');
            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });

        Schema::create('user_resource_access', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('access_type', 30);
            $table->foreignId('business_unit_id')->nullable()->constrained('business_units')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->string('subscription_id', 100)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'access_type']);
            $table->index(['access_type', 'business_unit_id']);
            $table->index(['access_type', 'project_id']);
            $table->index(['access_type', 'subscription_id']);
            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });

        Schema::table('user_role_assignments', function (Blueprint $table): void {
            $table->foreignId('resource_access_id')
                ->nullable()
                ->after('role_id')
                ->constrained('user_resource_access')
                ->cascadeOnDelete();

            $table->index('resource_access_id');
        });

        // Applications are the legacy name for projects. Preserve the legacy
        // tables/columns, but create a real Project record for each application
        // so the new hierarchy has only Business Unit -> Project -> Subscription.
        $applicationProjectMap = [];

        DB::table('applications')
            ->orderBy('id')
            ->get(['id', 'project_id', 'name', 'description', 'is_active'])
            ->each(function (object $application) use (&$applicationProjectMap): void {
                $parentProject = DB::table('projects')
                    ->where('id', $application->project_id)
                    ->first(['id', 'business_unit_id', 'name']);

                $businessUnitId = $parentProject?->business_unit_id;

                if ($businessUnitId === null && $parentProject !== null) {
                    $businessUnitId = DB::table('business_units')
                        ->where('name', $parentProject->name)
                        ->value('id');
                }

                $projectId = DB::table('projects')
                    ->where('name', $application->name)
                    ->value('id');

                if ($projectId === null) {
                    $projectId = DB::table('projects')->insertGetId([
                        'name' => $application->name,
                        'description' => $application->description,
                        'business_unit_id' => $businessUnitId,
                        'is_active' => (bool) $application->is_active,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($businessUnitId !== null) {
                    DB::table('projects')
                        ->where('id', $projectId)
                        ->whereNull('business_unit_id')
                        ->update(['business_unit_id' => $businessUnitId]);
                }

                $applicationProjectMap[(int) $application->id] = (int) $projectId;
            });

        // Move the existing application/environment subscription relationship
        // into the canonical project/subscription relationship. An environment
        // is optional; only rows that already have a valid environment are mapped.
        DB::table('azure_subscriptions')
            ->whereNotNull('application_id')
            ->whereNotNull('environment')
            ->orderBy('subscription_id')
            ->get(['subscription_id', 'application_id', 'environment'])
            ->each(function (object $subscription) use ($applicationProjectMap): void {
                $projectId = $applicationProjectMap[(int) $subscription->application_id] ?? null;
                $environment = strtoupper((string) $subscription->environment);

                if ($projectId === null || ! in_array($environment, ['PROD', 'DEV', 'QA'], true)) {
                    return;
                }

                $existing = DB::table('project_subscriptions')
                    ->where('subscription_id', $subscription->subscription_id)
                    ->first();

                if ($existing !== null) {
                    return;
                }

                $environmentKey = strtolower($environment);

                // A project can have at most one subscription for each environment.
                if (DB::table('project_subscriptions')
                    ->where('project_id', $projectId)
                    ->where('environment', $environmentKey)
                    ->exists()) {
                    return;
                }

                DB::table('project_subscriptions')->insert([
                    'project_id' => $projectId,
                    'subscription_id' => $subscription->subscription_id,
                    'environment' => $environmentKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // Convert every existing RBAC assignment into one of the three canonical
        // resource-access levels. Legacy columns remain untouched for compatibility.
        DB::table('user_role_assignments')
            ->orderBy('id')
            ->get([
                'id', 'user_id', 'role_id', 'scope_type', 'business_unit_id',
                'project_id', 'application_id', 'environment', 'subscription_id',
                'granted_by_user_id', 'starts_at', 'expires_at',
            ])
            ->each(function (object $assignment) use ($applicationProjectMap): void {
                $accessType = null;
                $businessUnitId = null;
                $projectId = null;
                $subscriptionId = null;

                switch ((string) $assignment->scope_type) {
                    case 'business_unit':
                    case 'team':
                        if ($assignment->business_unit_id !== null) {
                            $accessType = 'business_unit';
                            $businessUnitId = (int) $assignment->business_unit_id;
                        }
                        break;

                    case 'project':
                        if ($assignment->project_id !== null) {
                            $accessType = 'project';
                            $projectId = (int) $assignment->project_id;
                        }
                        break;

                    case 'application':
                        $projectId = $assignment->application_id !== null
                            ? ($applicationProjectMap[(int) $assignment->application_id] ?? null)
                            : null;

                        if ($projectId !== null) {
                            $accessType = 'project';
                        }
                        break;

                    case 'environment':
                        if ($assignment->subscription_id !== null) {
                            $accessType = 'subscription';
                            $subscriptionId = (string) $assignment->subscription_id;
                        } elseif ($assignment->application_id !== null && $assignment->environment !== null) {
                            $mappedProjectId = $applicationProjectMap[(int) $assignment->application_id] ?? null;

                            if ($mappedProjectId !== null) {
                                $subscriptionId = DB::table('project_subscriptions')
                                    ->where('project_id', $mappedProjectId)
                                    ->where('environment', strtolower((string) $assignment->environment))
                                    ->value('subscription_id');

                                if ($subscriptionId !== null) {
                                    $accessType = 'subscription';
                                }
                            }
                        }
                        break;

                    case 'subscription':
                        if ($assignment->subscription_id !== null) {
                            $accessType = 'subscription';
                            $subscriptionId = (string) $assignment->subscription_id;
                        }
                        break;
                }

                if ($accessType === null) {
                    return;
                }

                $query = DB::table('user_resource_access')
                    ->where('user_id', $assignment->user_id)
                    ->where('access_type', $accessType);

                if ($accessType === 'business_unit') {
                    $query->where('business_unit_id', $businessUnitId);
                } elseif ($accessType === 'project') {
                    $query->where('project_id', $projectId);
                } else {
                    $query->where('subscription_id', $subscriptionId);
                }

                $resourceAccessId = $query->value('id');

                if ($resourceAccessId === null) {
                    $resourceAccessId = DB::table('user_resource_access')->insertGetId([
                        'user_id' => $assignment->user_id,
                        'access_type' => $accessType,
                        'business_unit_id' => $businessUnitId,
                        'project_id' => $projectId,
                        'subscription_id' => $subscriptionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('user_role_assignments')
                    ->where('id', $assignment->id)
                    ->update(['resource_access_id' => $resourceAccessId]);
            });
    }

    public function down(): void
    {
        Schema::table('user_role_assignments', function (Blueprint $table): void {
            $table->dropForeign(['resource_access_id']);
            $table->dropIndex(['resource_access_id']);
            $table->dropColumn('resource_access_id');
        });

        Schema::dropIfExists('user_resource_access');
        Schema::dropIfExists('project_subscriptions');
    }
};
