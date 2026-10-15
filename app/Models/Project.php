<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','business_unit_id','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function subscriptionAssignments(): HasMany { return $this->hasMany(ProjectSubscription::class); }
    public function subscriptions(): BelongsToMany { return $this->belongsToMany(AzureSubscription::class,'project_subscriptions','project_id','subscription_id','id','subscription_id')->withPivot('environment'); }
}
