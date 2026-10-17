<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedJob extends Model
{
    public $timestamps = false;

    protected $table = 'failed_jobs';

    protected $guarded = [];

    protected $casts = [
        'failed_at' => 'datetime',
    ];

    public function jobClass(): string
    {
        $payload = json_decode((string) $this->payload, true);

        return (string) ($payload['displayName'] ?? data_get($payload, 'data.commandName') ?? 'Unknown job');
    }
}
