<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        DB::table('notification_rules')
            ->where('severity', 'danger')
            ->update(['severity' => 'critical']);
    }
};
