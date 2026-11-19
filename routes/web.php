<?php

use App\Http\Controllers\ClientPortalNotificationController;
use App\Http\Controllers\SubscriptionMetricsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function (): void {
    Route::get('/data', [ClientPortalNotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [ClientPortalNotificationController::class, 'read'])->name('read');
    Route::post('/read-all', [ClientPortalNotificationController::class, 'readAll'])->name('mark-all-read');
});

Route::middleware('auth:web,developers')->prefix('subscriptions/{subscription:subscription_id}/metrics')->name('subscriptions.metrics.')->group(function (): void {
    Route::get('/resources', [SubscriptionMetricsController::class, 'resources'])->name('resources');
    Route::get('/graphs', [SubscriptionMetricsController::class, 'graphs'])->name('graphs');
    Route::post('/graphs', [SubscriptionMetricsController::class, 'storeGraph'])->name('graphs.store');
    Route::delete('/graphs/{graph}', [SubscriptionMetricsController::class, 'deleteGraph'])->name('graphs.destroy');
    Route::get('/graphs/{graph}/data', [SubscriptionMetricsController::class, 'data'])->name('graphs.data');
});

Route::middleware('auth')->get('/sync-report/latest', function () {
    $path = storage_path('logs/scheduler/reports/latest.json');

    abort_unless(is_file($path), 404, 'No synchronization report has been generated yet.');

    $report = json_decode((string) file_get_contents($path), true);

    abort_unless(is_array($report) && isset($report['modules']), 404, 'The synchronization report is invalid.');

    return response()
        ->view('reports.sync-report', ['report' => $report])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache');
})->name('sync-report.latest');