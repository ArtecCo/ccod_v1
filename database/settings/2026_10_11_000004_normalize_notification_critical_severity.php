<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        DB::table('notifications')
            ->select(['id', 'data'])
            ->orderBy('id')
            ->chunkById(100, function ($notifications): void {
                foreach ($notifications as $notification) {
                    $data = json_decode($notification->data, true);

                    if (! is_array($data) || ($data['severity'] ?? null) !== 'danger') {
                        continue;
                    }

                    $data['severity'] = 'critical';

                    DB::table('notifications')
                        ->where('id', $notification->id)
                        ->update(['data' => json_encode($data)]);
                }
            });
    }
};
