<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams_webhook_templates', function (Blueprint $table): void {
            $table->string('event_key', 120)->nullable()->after('category')->index();
        });

        DB::table('teams_webhook_templates')->where('category', 'server_start')
            ->update(['event_key' => 'server.started']);
        DB::table('teams_webhook_templates')->where('category', 'sync_status')
            ->update(['event_key' => 'sync.completed']);

        DB::table('teams_webhooks')->orderBy('id')->get()->each(function ($webhook): void {
            $keys = json_decode($webhook->event_keys ?? '[]', true);
            if (! is_array($keys)) {
                $keys = [];
            }

            $keys = array_values(array_unique(array_map(
                static fn ($key) => $key === 'azure.sync.completed' ? 'sync.completed' : $key,
                $keys,
            )));

            DB::table('teams_webhooks')->where('id', $webhook->id)->update([
                'event_keys' => json_encode($keys),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('teams_webhooks')->orderBy('id')->get()->each(function ($webhook): void {
            $keys = json_decode($webhook->event_keys ?? '[]', true);
            if (! is_array($keys)) {
                return;
            }

            $keys = array_map(
                static fn ($key) => $key === 'sync.completed' ? 'azure.sync.completed' : $key,
                $keys,
            );

            DB::table('teams_webhooks')->where('id', $webhook->id)->update([
                'event_keys' => json_encode(array_values(array_unique($keys))),
            ]);
        });

        Schema::table('teams_webhook_templates', function (Blueprint $table): void {
            $table->dropIndex(['event_key']);
            $table->dropColumn('event_key');
        });
    }
};
