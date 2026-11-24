<?php

use Illuminate\Database\\Migrations\\Migration;
use Illuminate\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('subscription_web_urls', 'use_proxy')) {
            Schema::table('subscription_web_urls', function (Blueprint $table): void {
                $table->dropColumn('use_proxy');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('subscription_web_urls', 'use_proxy')) {
            Schema::table('subscription_web_urls', function (Blueprint $table): void {
                $table->boolean('use_proxy')->default(false)->after('is_active');
            });
        }
    }
};
