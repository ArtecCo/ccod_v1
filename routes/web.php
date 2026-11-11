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

Route::middleware('auth:developers')->get('/developer/sync-report', function () {
    abort_unless(auth('developers')->user()?->is_active === true, 403);

    $report = storage_path('logs/scheduler/reports/latest.html');
    abort_unless(is_file($report), 404, 'No synchronization report has been generated yet.');

    return response()->file($report, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'Cache-Control' => 'private, no-store, max-age=0',
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->name('developer.sync-report');
