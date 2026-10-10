<?php

namespace App\Filament\Resources\DatabaseTables\Pages;

use App\Filament\Resources\DatabaseTables\DatabaseTableResource;
use Filament\Resources\Pages\ListRecords;

class ListDatabaseTables extends ListRecords
{
    protected static string $resource = DatabaseTableResource::class;

    public function getTitle(): string
    {
        return 'Database Tables';
    }
}
