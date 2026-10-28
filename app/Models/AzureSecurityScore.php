<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AzureSecurityScore extends Model
{
    protected $table = 'azure_security_scores';

    public $timestamps = false;

    protected $fillable = [
        'subscription_id',
        'initiative_name',
        'current_score',
        'max_score',
        'percentage_score',
        'captured_at',
        'synced_at',
    ];

    protected $casts = [
        'current_score' => 'decimal:4',
        'max_score' => 'decimal:4',
        'percentage_score' => 'decimal:5',
        'captured_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id');
    }
}
