<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The subscription identifier is used as a string primary/foreign-key value
        // across the authorization tables. Keep every participating column on the
        // same charset/collation so MySQL comparisons cannot fail with error 1267.
        DB::statement('ALTER TABLE azure_subscriptions MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE team_subscription MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE access_requests MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        DB::statement('ALTER TABLE user_access_grants MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
    }

    public function down(): void
    {
        // Keep the schema on the application's canonical collation when rolling back.
        // Reverting to a mixed collation would reintroduce the original MySQL error.
        DB::statement('ALTER TABLE azure_subscriptions MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE team_subscription MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE access_requests MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        DB::statement('ALTER TABLE user_access_grants MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
    }
};
