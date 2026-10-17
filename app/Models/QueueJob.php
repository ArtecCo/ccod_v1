<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJob extends Model
{
    public $timestamps = false;

    protected $table = 'jobs';

    protected $guarded = [];

    public function jobClass(): string
    {
        $payload = json_decode((string) $this->payload, true);

        return (string) ($payload['displayName'] ?? data_get($payload, 'data.commandName') ?? 'Unknown job');
    }
}
