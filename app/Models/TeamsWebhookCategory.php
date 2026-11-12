<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeamsWebhookCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $base = Str::slug($category->name);
            $slug = $base;
            $suffix = 2;

            while (static::query()->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$suffix++;
            }

            $category->slug = $slug;
        });
    }

    public function templates()
    {
        return $this->hasMany(TeamsWebhookTemplate::class, 'category', 'slug');
    }
}
