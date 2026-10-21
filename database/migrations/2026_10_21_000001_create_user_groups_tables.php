<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_groups', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
            $table->timestamps();
        });

        Schema::create('user_group_user', function (Blueprint $table): void {
            $table->foreignId('user_group_id')->constrained('user_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['user_group_id', 'user_id']);
        });

        Schema::create('project_user_group', function (Blueprint $table): void {
            $table->foreignId('user_group_id')->constrained('user_groups')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_group_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user_group');
        Schema::dropIfExists('user_group_user');
        Schema::dropIfExists('user_groups');
    }
};
