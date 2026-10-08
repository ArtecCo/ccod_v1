<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->string('key_vault_reference', 255)->nullable()->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->dropColumn('key_vault_reference');
        });
    }
};
