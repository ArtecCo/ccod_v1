<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams_webhook_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('category', 40)->index();
            $table->string('title_template', 200)->nullable();
            $table->text('message_template');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['category', 'is_default']);
        });

        $now = now();
        $templates = [
            [
                'name' => 'Default server start',
                'category' => 'server_start',
                'title_template' => 'CCOD server startup',
                'message_template' => "**Server boot time:** {server_boot_time}\\n\\n**IIS**\\n- **Status:** {iis_status}\\n- **Start time:** {iis_started_at}\\n\\n**PHP FastCGI**\\n- **Status:** {php_status}\\n- **Start time:** {php_started_at}\\n\\n**MySQL2610**\\n- **Status:** {mysql_status}\\n- **Start time:** {mysql_started_at}\\n\\n**Laravel queue**\\n- **Status:** {queue_status}\\n- **Start time:** {queue_started_at}",
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Default sync status',
                'category' => 'sync_status',
                'title_template' => 'CCOD Azure sync status',
                'message_template' => "**Overall status:** {overall_status}\\n**Started:** {sync_started_at}\\n**Completed:** {completed_at}\\n**Duration:** {duration}\\n\\n**Module status**\\n- **Subscriptions:** {subscriptions_status}\\n- **Resources:** {resources_status}\\n- **Costs:** {costs_status}\\n- **Security:** {security_status}\\n- **Alerts:** {alerts_status}\\n- **Web URLs:** {web_urls_status}",
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('teams_webhook_templates')->insert($template);
        }

        Schema::table('teams_webhooks', function (Blueprint $table): void {
            $table->foreignId('template_id')->nullable()->after('category')
                ->constrained('teams_webhook_templates')->nullOnDelete();
        });

        foreach (DB::table('teams_webhook_templates')->get() as $template) {
            DB::table('teams_webhooks')->where('category', $template->category)
                ->whereNull('template_id')->update(['template_id' => $template->id]);
        }
    }

    public function down(): void
    {
        Schema::table('teams_webhooks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('template_id');
        });

        Schema::dropIfExists('teams_webhook_templates');
    }
};
