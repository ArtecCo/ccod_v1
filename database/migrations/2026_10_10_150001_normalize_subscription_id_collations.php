<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // A previous failed migration may already have removed one or more
        // foreign keys. Discover the constraints that actually exist before
        // changing the subscription identifier columns.
        $foreignKeys = DB::select(<<<'SQL'
            SELECT
                kcu.TABLE_NAME AS table_name,
                kcu.CONSTRAINT_NAME AS constraint_name,
                kcu.COLUMN_NAME AS column_name,
                rc.UPDATE_RULE AS update_rule,
                rc.DELETE_RULE AS delete_rule
            FROM information_schema.KEY_COLUMN_USAGE AS kcu
            INNER JOIN information_schema.REFERENTIAL_CONSTRAINTS AS rc
                ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
                AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                AND rc.TABLE_NAME = kcu.TABLE_NAME
            WHERE kcu.CONSTRAINT_SCHEMA = DATABASE()
              AND kcu.REFERENCED_TABLE_NAME = 'azure_subscriptions'
              AND kcu.REFERENCED_COLUMN_NAME = 'subscription_id'
        SQL);

        foreach ($foreignKeys as $foreignKey) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                $foreignKey->table_name,
                $foreignKey->constraint_name,
            ));
        }

        DB::statement('ALTER TABLE azure_subscriptions MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE billing_resources MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE team_subscription MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        DB::statement('ALTER TABLE access_requests MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
        DB::statement('ALTER TABLE user_access_grants MODIFY subscription_id VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');

        foreach ($foreignKeys as $foreignKey) {
            $onUpdate = strtoupper($foreignKey->update_rule);
            $onDelete = strtoupper($foreignKey->delete_rule);
            $updateClause = $onUpdate !== 'RESTRICT' ? " ON UPDATE {$onUpdate}" : '';
            $deleteClause = $onDelete !== 'RESTRICT' ? " ON DELETE {$onDelete}" : '';

            DB::statement(sprintf(
                'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `azure_subscriptions` (`subscription_id`)%s%s',
                $foreignKey->table_name,
                $foreignKey->constraint_name,
                $foreignKey->column_name,
                $deleteClause,
                $updateClause,
            ));
        }

        // team_subscription is required by the authorization layer. The
        // previous failed migration can leave this FK absent, so restore it
        // only when it is not already present.
        $exists = DB::selectOne(<<<'SQL'
            SELECT 1
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = DATABASE()
              AND TABLE_NAME = 'team_subscription'
              AND CONSTRAINT_NAME = 'team_subscription_subscription_id_foreign'
            LIMIT 1
        SQL);

        if ($exists === null) {
            DB::statement(
                'ALTER TABLE team_subscription ADD CONSTRAINT team_subscription_subscription_id_foreign FOREIGN KEY (subscription_id) REFERENCES azure_subscriptions (subscription_id) ON DELETE CASCADE'
            );
        }
    }

    public function down(): void
    {
        // Keep the canonical utf8mb4_unicode_ci collation. Reverting it would
        // intentionally reintroduce the MySQL collation mismatch.
    }
};
