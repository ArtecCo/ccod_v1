<?php

use App\\Models\\TeamsWebhookCategory;
use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams_webhook_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        $now = now();
        $defaults = [
            ['name' => 'Server start', 'slug' => 'server_start', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sync status', 'slug' => 'sync_status', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($defaults as $category) {
            DB::table('teams_webhook_categories')->insertOrIgnore($category);
        }

        $existing = DB::table('teams_webhook_templates')->select('category')->distinct()->pluck('category');
        foreach ($existing as $slug) {
            if (! DB::table('teams_webhook_categories')->where('slug', $slug)->exists()) {
                DB::table('teams_webhook_categories')->insert([
                    'name' => str($slug)->replace('_', ' ')->title()->toString(),
                    'slug' => $slug,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teams_webhook_categories');
    }
};
