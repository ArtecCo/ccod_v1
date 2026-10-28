<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('azure_security_score_controls')) {
            Schema::drop('azure_security_score_controls');
        }

        if (Schema::hasTable('azure_security_scores') && Schema::hasColumn('azure_security_scores', 'weight')) {
            Schema::table('azure_security_scores', function ($table): void {
                $table->dropColumn('weight');
            });
        }
    }

    public function down(): void
    {
        // Intentionally irreversible: the removed control data and weight field
        // are no longer part of the CCOD security-score model.
    }
};
