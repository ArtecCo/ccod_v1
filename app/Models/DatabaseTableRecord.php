<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseTableRecord extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';

    protected $table = '';

    public function setTable($table): static
    {
        $this->table = $table;

        return $this;
    }
}
