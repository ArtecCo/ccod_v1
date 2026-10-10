<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('user_subscription_access_overrides')) {
            Schema::create('user_subscription_access_overrides', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('subscription_id');
                $table->string('override')->default('revoked');
                $table->string('revoked_by_type')->nullable();
                $table->unsignedBigInteger('revoked_by_id')->nullable();
                $table->timestamp('revoked_at')->nullable();
                $table->text('reason')->nullable();
                $table->timestamps();
            });
        }

        $indexes = DB::select('SHOW INDEX FROM `user_subscription_access_overrides`');
        $indexNames = collect($indexes)
            ->pluck('Key_name')
            ->unique()
            ->all();

        if (! in_array('user_sub_access_overrides_unique', $indexNames, true)) {
            Schema::table('user_subscription_access_overrides', function (Blueprint $table): void {
                $table->unique(['user_id', 'subscription_id'], 'user_sub_access_overrides_unique');
            });
        }

        if (! in_array('user_sub_access_overrides_subscription_override_index', $indexNames, true)) {
            Schema::table('user_subscription_access_overrides', function (Blueprint $table): void {
                $table->index(['subscription_id', 'override'], 'user_sub_access_overrides_subscription_override_index');
            });
        }

        if (! in_array('user_sub_access_overrides_revoked_by_index', $indexNames, true)) {
            Schema::table('user_subscription_access_overrides', function (Blueprint $table): void {
                $table->index(['revoked_by_type', 'revoked_by_id'], 'user_sub_access_overrides_revoked_by_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscription_access_overrides');
    }
};
