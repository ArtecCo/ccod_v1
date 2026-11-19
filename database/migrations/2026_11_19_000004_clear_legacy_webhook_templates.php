<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove only seeded defaults. Preserve every user-authored template.
        DB::table('teams_webhook_templates')->where('is_default', true)->delete();
    }

    public function down(): void
    {
        // User-created templates cannot be reconstructed safely.
    }
};
