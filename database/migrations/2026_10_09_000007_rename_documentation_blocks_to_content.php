<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('documentations') || ! Schema::hasColumn('documentations', 'blocks')) {
            return;
        }

        Schema::table('documentations', function (Blueprint $table): void {
            $table->renameColumn('blocks', 'content');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('documentations') || ! Schema::hasColumn('documentations', 'content')) {
            return;
        }

        Schema::table('documentations', function (Blueprint $table): void {
            $table->renameColumn('content', 'blocks');
        });
    }
};
