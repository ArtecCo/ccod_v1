<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AzureSubscription extends Model
{
    protected $table = 'azure_subscriptions';
    protected $primaryKey = 'subscription_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['subscription_id','display_name','state','tenant_id','authorization_source','application_id','environment','key_vault_reference','health_status','security_score','mtd_spend_eur','last_synced'];
    protected $casts = ['security_score'=>'decimal:2','mtd_spend_eur'=>'decimal:2','last_synced'=>'datetime'];

    /** @deprecated Legacy metadata retained for migration compatibility. */
    public function application(): BelongsTo { return $this->belongsTo(Application::class); }
    public function projects(): BelongsToMany { return $this->belongsToMany(Project::class,'project_subscriptions','subscription_id','project_id','subscription_id','id')->withPivot('environment'); }
    public function businessUnits(): BelongsToMany { return $this->belongsToMany(BusinessUnit::class,'team_subscription','subscription_id','business_unit_id','subscription_id','id')->withTimestamps(); }
    /** @deprecated Use businessUnits(). */
    public function teams(): BelongsToMany { return $this->businessUnits(); }
    public function documentation(): HasOne { return $this->hasOne(SubscriptionDocumentation::class,'subscription_id','subscription_id'); }
    public function budgets(): HasMany { return $this->hasMany(AzureBudget::class,'subscription_id','subscription_id'); }
    public function costForecast(): HasOne { return $this->hasOne(AzureCostForecast::class,'subscription_id','subscription_id'); }
    public function billingResources(): HasMany { return $this->hasMany(BillingResource::class,'subscription_id','subscription_id'); }
    public function webUrls(): HasMany { return $this->hasMany(SubscriptionWebUrl::class,'subscription_id','subscription_id'); }
    public function alerts(): HasMany { return $this->hasMany(AzureAlert::class,'subscription_id','subscription_id'); }
    public function costs(): HasMany { return $this->hasMany(AzureSubscriptionCost::class,'subscription_id','subscription_id'); }
    public function securityAssessments(): HasMany { return $this->hasMany(AzureSecurityAssessment::class,'subscription_id','subscription_id'); }
}
