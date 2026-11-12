<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class TeamsWebhookTemplate extends Model
{
    protected $fillable = ['name', 'category', 'title_template', 'message_template', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $template): void {
            if ($template->is_default) {
                static::query()
                    ->where('category', $template->category)
                    ->when($template->exists, fn ($query) => $query->whereKeyNot($template->getKey()))
                    ->update(['is_default' => false]);
            }
        });

        static::deleted(function (self $template): void {
            if ($template->is_default) {
                $replacement = static::query()->where('category', $template->category)->orderBy('id')->first();
                $replacement?->forceFill(['is_default' => true])->save();
            }
        });
    }

    public function webhooks()
    {
        return $this->hasMany(TeamsWebhook::class, 'template_id');
    }
}
