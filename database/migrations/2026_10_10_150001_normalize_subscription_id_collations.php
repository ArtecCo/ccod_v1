<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // All tables that reference azure_subscriptions.subscription_id must use
        // the same charset/collation as the parent column. Drop the foreign keys
        // temporarily so MySQL permits the child-column changes.
        DB::statement('ALTER TABLE billing_resources DROP FOREIGN KEY billing_resources_ibfk_1');
        DB::statement('ALTER TABLE team_subscription DROP FOREIGN KEY team_subscription_subscription_id_foreign');

        DB::statement('ALTER TABLE billing_resources MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE team_subscription MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE access_requests MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        DB::statement('ALTER TABLE user_access_grants MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');

        DB::statement('ALTER TABLE billing_resources ADD CONSTRAINT billing_resources_ibfk_1 FOREIGN KEY (subscription_id) REFERENCES azure_subscriptions (subscription_id) ON DELETE CASCADE');
        DB::statement('ALTER TABLE team_subscription ADD CONSTRAINT team_subscription_subscription_id_foreign FOREIGN KEY (subscription_id) REFERENCES azure_subscriptions (subscription_id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        // Keep the canonical utf8mb4_unicode_ci collation. Reverting it would
        // intentionally reintroduce the MySQL collation mismatch.
    }
};
