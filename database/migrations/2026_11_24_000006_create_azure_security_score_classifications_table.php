<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL may leave the table in place if adding the index fails.
        // Keep this migration safe to retry after that partial execution.
        if (! Schema::hasTable('azure_security_score_classifications')) {
            Schema::create('azure_security_score_classifications', function (Blueprint $table): void {
                $table->id();
                $table->decimal('minimum_score', 5, 2);
                $table->decimal('maximum_score', 5, 2);
                $table->string('severity', 20);
                $table->boolean('enabled')->default(true);
                $table->timestamps();
            });
        }

        $indexName = 'az_ssc_enabled_score_idx';
        $existingIndex = DB::select(
            'SHOW INDEX FROM azure_security_score_classifications WHERE Key_name = ?',
            [$indexName]
        );

        if ($existingIndex === []) {
            Schema::table('azure_security_score_classifications', function (Blueprint $table) use ($indexName): void {
                $table->index(['enabled', 'minimum_score', 'maximum_score'], $indexName);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_security_score_classifications');
    }
};
