<?php

namespace App\Models;

use CarlJanzell\FilamentPageBuilder\Concerns\HasBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documentation extends Model
{
    use HasBlocks;

    protected $fillable = [
        'title',
        'slug',
        'blocks',
        'author_id',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
