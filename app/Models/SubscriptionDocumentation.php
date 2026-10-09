<?php

namespace App\Models;

use CarlJanzell\FilamentPageBuilder\Concerns\HasBlocks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionDocumentation extends Model
{
    use HasBlocks;

    protected $fillable = [
        'subscription_id',
        'blocks',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
