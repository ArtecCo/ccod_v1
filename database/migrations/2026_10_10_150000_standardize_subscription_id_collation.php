<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // team_subscription has a foreign key to azure_subscriptions.subscription_id.
        // Drop it while the two columns are normalized, then recreate it.
        DB::statement('ALTER TABLE team_subscription DROP FOREIGN KEY team_subscription_subscription_id_foreign');

        DB::statement('ALTER TABLE azure_subscriptions MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE team_subscription MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE access_requests MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        DB::statement('ALTER TABLE user_access_grants MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');

        DB::statement('ALTER TABLE team_subscription ADD CONSTRAINT team_subscription_subscription_id_foreign FOREIGN KEY (subscription_id) REFERENCES azure_subscriptions (subscription_id) ON DELETE CASCADE');
    }

    public function down(): void
    {
        // Do not restore the old mixed-collation state. The canonical schema uses
        // utf8mb4_unicode_ci for subscription identifiers.
    }
};
