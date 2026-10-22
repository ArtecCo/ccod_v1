<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('user_subscription_access_override');
        Schema::dropIfExists('user_role_assignments');
        Schema::dropIfExists('user_access_grants');
        Schema::dropIfExists('user_resource_access');
        Schema::dropIfExists('audit_logs');
    }

    public function down(): void
    {
        // These tables belong to the retired authorization model.
        // They are intentionally not recreated.
    }
};