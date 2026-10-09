<?php

namespace App\Providers;

use App\Models\Documentation;
use App\Policies\DocumentationPolicy;
use Filament\Forms\Components\Field;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Documentation::class, DocumentationPolicy::class);

        // 1. Force all data tables across the app to use compact row layouts
        Table::configureUsing(function (Table $table): void {
            $table
                ->striped()
                ->extraAttributes([
                    'class' => 'compact-table [&_td]:py-1 [&_th]:py-1'
                ]);
        });

        // 2. Align labels horizontally for ALL field types to save massive vertical space
        Field::configureUsing(function (Field $field): void {
            $field->inlineLabel();
        });
    }
}
