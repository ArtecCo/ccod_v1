<?php

namespace App\Filament\Resources\QueueJobs\Pages;

use App\Filament\Resources\QueueJobs\QueueJobResource;
use Filament\Resources\Pages\ListRecords;

class ListQueueJobs extends ListRecords
{
    protected static string $resource = QueueJobResource::class;
}
