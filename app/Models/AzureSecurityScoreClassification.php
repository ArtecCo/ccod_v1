<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AzureSecurityScoreClassification extends Model
{
    protected $fillable = ['minimum_score', 'maximum_score', 'severity', 'enabled'];

    protected function casts(): array
    {
        return [
            'minimum_score' => 'decimal:2',
            'maximum_score' => 'decimal:2',
            'enabled' => 'boolean',
        ];
    }
}
