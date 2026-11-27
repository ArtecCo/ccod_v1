<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_exports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('developer_id')->constrained('developers')->cascadeOnDelete();
            $table->string('scope_type', 30);
            $table->string('scope_id', 100);
            $table->string('scope_name');
            $table->string('timeline', 20);
            $table->date('period_start');
            $table->date('period_end');
            $table->json('report_types');
            $table->string('status', 20)->default('queued');
            $table->json('files')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['developer_id', 'status']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_exports');
    }
};
