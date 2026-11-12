<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams_webhooks', function (Blueprint $table): void {
            $table->json('event_keys')->nullable()->after('template_id');
            $table->json('conditions')->nullable()->after('event_keys');
            $table->string('condition_mode', 10)->default('all')->after('conditions');
        });

        DB::table('teams_webhooks')->orderBy('id')->get()->each(function ($webhook): void {
            $eventKeys = match ($webhook->category) {
                'server_start' => ['server.started'],
                'sync_status' => ['azure.sync.completed'],
                default => [],
            };

            DB::table('teams_webhooks')->where('id', $webhook->id)->update([
                'event_keys' => json_encode($eventKeys),
                'conditions' => json_encode([]),
                'condition_mode' => 'all',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('teams_webhooks', function (Blueprint $table): void {
            $table->dropColumn(['event_keys', 'conditions', 'condition_mode']);
        });
    }
};
