<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExport extends Model
{
    protected $fillable = [
        'developer_id',
        'scope_type',
        'scope_id',
        'scope_name',
        'timeline',
        'period_start',
        'period_end',
        'report_types',
        'status',
        'files',
        'failure_reason',
        'completed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'report_types' => 'array',
            'files' => 'array',
            'period_start' => 'date',
            'period_end' => 'date',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }
}
