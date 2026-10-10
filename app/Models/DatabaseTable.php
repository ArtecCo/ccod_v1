<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseTable extends Model
{
    protected $table = 'information_schema.tables';

    protected $primaryKey = 'table_name';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'table_rows' => 'integer',
            'data_length' => 'integer',
            'index_length' => 'integer',
            'size_mb' => 'float',
        ];
    }
}
