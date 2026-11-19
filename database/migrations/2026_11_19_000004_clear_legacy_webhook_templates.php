<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Templates are user-authored configuration. Remove the seeded and
        // previously registered templates; nullable foreign keys on rules
        // and destinations are intentionally set to null by the database.
        DB::table('teams_webhook_templates')->delete();
    }

    public function down(): void
    {
        // User-created templates cannot be reconstructed safely.
    }
};
