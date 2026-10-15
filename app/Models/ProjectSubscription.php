<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSubscription extends Model
{
    protected $fillable = ['project_id', 'subscription_id', 'environment'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function subscription(): BelongsTo { return $this->belongsTo(AzureSubscription::class, 'subscription_id', 'subscription_id'); }
}
