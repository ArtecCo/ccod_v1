<?php

declare(strict_types=1);

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Azure Secure Score returns percentage as a 0–1 fraction.
        // Normalize existing snapshots to whole percentages (0–100).
        DB::table('azure_security_scores')
            ->update(['percentage_score' => DB::raw('percentage_score * 100')]);
    }

    public function down(): void
    {
        // Restore the original fractional representation.
        DB::table('azure_security_scores')
            ->update(['percentage_score' => DB::raw('percentage_score / 100')]);
    }
};
