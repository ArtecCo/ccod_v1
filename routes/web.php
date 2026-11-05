<?php

use App\Http\Controllers\ClientPortalNotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function (): void {
    Route::get('/data', [ClientPortalNotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [ClientPortalNotificationController::class, 'read'])->name('read');
    Route::post('/read-all', [ClientPortalNotificationController::class, 'readAll'])->name('mark-all-read');
});

Route::middleware('auth:web')->prefix('subscriptions/{subscription:subscription_id}/metrics')->name('subscriptions.metrics.')->group(function (): void {
    Route::get('/resources', [SubscriptionMetricsController::class, 'resources'])->name('resources');
    Route::get('/graphs', [SubscriptionMetricsController::class, 'graphs'])->name('graphs');
    Route::post('/graphs', [SubscriptionMetricsController::class, 'storeGraph'])->name('graphs.store');
    Route::delete('/graphs/{graph}', [SubscriptionMetricsController::class, 'deleteGraph'])->name('graphs.destroy');
    Route::get('/graphs/{graph}/data', [SubscriptionMetricsController::class, 'data'])->name('graphs.data');
});
