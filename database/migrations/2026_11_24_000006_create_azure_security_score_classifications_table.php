<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_security_score_classifications', function (Blueprint $table): void {
            $table->id();
            $table->decimal('minimum_score', 5, 2);
            $table->decimal('maximum_score', 5, 2);
            $table->string('severity', 20);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->index(['enabled', 'minimum_score', 'maximum_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_security_score_classifications');
    }
};
