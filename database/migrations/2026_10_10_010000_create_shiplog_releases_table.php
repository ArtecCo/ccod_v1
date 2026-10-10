<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shiplog_releases', function (Blueprint $table): void {
            $table->id();
            $table->string('version')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->date('released_at')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->json('environments')->nullable();
            $table->boolean('yanked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shiplog_releases');
    }
};
