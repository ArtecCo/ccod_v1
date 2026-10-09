<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentations', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('blocks')->nullable();
            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('subscription_documentations')) {
            $legacyRows = \Illuminate\Support\Facades\DB::table('subscription_documentations')
                ->join('azure_subscriptions', 'subscription_documentations.subscription_id', '=', 'azure_subscriptions.subscription_id')
                ->select(
                    'subscription_documentations.blocks',
                    'subscription_documentations.created_at',
                    'subscription_documentations.updated_at',
                    'azure_subscriptions.display_name'
                )
                ->get();

            foreach ($legacyRows as $row) {
                $title = trim($row->display_name . ' Documentation');
                $slug = \Illuminate\Support\Str::slug($title);
                $baseSlug = $slug;
                $suffix = 2;

                while (\Illuminate\Support\Facades\DB::table('documentations')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $suffix++;
                }

                \Illuminate\Support\Facades\DB::table('documentations')->insert([
                    'title' => $title,
                    'slug' => $slug,
                    'blocks' => $row->blocks,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            Schema::drop('subscription_documentations');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documentations');
    }
};
