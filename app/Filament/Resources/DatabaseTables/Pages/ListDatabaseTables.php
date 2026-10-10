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

    public function getTableRecordUrl($record): ?string
    {
        return DatabaseTableResource::getUrl('view', [
            'table' => $record->table_name,
        ]);
    }
}
