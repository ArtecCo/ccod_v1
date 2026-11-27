<?php

namespace App\Http\Controllers;

use App\Models\ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportExportDownloadController extends Controller
{
    public function download(Request $request, ReportExport $report, string $file)
    {
        abort_unless((int) $report->developer_id === (int) $request->user('developers')?->getKey(), 404);
        abort_unless($report->status === 'completed' && ! $report->expires_at?->isPast(), 404);

        $entry = collect($report->files ?? [])->firstWhere('key', $file);
        abort_unless(is_array($entry) && isset($entry['path'], $entry['name']), 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($entry['path']), 404);

        return $disk->download($entry['path'], $entry['name'], [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
