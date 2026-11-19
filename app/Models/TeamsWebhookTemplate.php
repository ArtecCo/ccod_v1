<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamsWebhookTemplate extends Model
{
    protected $fillable = ['name', 'event_key', 'title_template', 'message_template'];
}
