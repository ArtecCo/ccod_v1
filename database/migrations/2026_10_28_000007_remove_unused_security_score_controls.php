<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('azure_security_score_controls');

        if (Schema::hasColumn('azure_security_scores', 'weight')) {
            Schema::table('azure_security_scores', function (Blueprint $table): void {
                $table->dropColumn('weight');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('azure_security_scores') && ! Schema::hasColumn('azure_security_scores', 'weight')) {
            Schema::table('azure_security_scores', function (Blueprint $table): void {
                $table->unsignedBigInteger('weight')->default(0)->after('percentage_score');
            });
        }
    }
};
