<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AzureSecuritySummary extends Model
{
    protected $table = 'azure_security_summary';

    protected $primaryKey = 'summary_id';

    public $incrementing = false;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'summary_id',
        'weighted_score',
        'total_weight',
        'fetched_at',
    ];

    protected $casts = [
        'summary_id' => 'integer',
        'weighted_score' => 'decimal:4',
        'total_weight' => 'decimal:4',
        'fetched_at' => 'datetime',
    ];
}