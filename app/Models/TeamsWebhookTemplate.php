<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamsWebhookTemplate extends Model
{
    protected $fillable = ['name', 'category', 'event_key', 'title_template', 'message_template'];

    public function webhooks()
    {
        return $this->hasMany(TeamsWebhook::class, 'template_id');
    }
}
