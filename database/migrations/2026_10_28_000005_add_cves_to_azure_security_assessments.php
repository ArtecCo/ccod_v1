<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('azure_security_assessments', 'cves')) {
            Schema::table('azure_security_assessments', function (Blueprint $table): void {
                $table->json('cves')->nullable()->after('threats');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('azure_security_assessments', 'cves')) {
            Schema::table('azure_security_assessments', function (Blueprint $table): void {
                $table->dropColumn('cves');
            });
        }
    }
};
