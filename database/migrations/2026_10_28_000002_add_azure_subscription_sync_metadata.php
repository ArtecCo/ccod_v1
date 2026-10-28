<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->string('state', 20)->nullable()->after('display_name');
            $table->string('tenant_id', 36)->nullable()->after('state');
            $table->string('authorization_source', 100)->nullable()->after('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->dropColumn([
                'state',
                'tenant_id',
                'authorization_source',
            ]);
        });
    }
};
