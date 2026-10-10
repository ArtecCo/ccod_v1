<?php

use App\Http\Controllers\ClientPortalNotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function (): void {
    Route::get('/', [ClientPortalNotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [ClientPortalNotificationController::class, 'read'])->name('read');
    Route::post('/read-all', [ClientPortalNotificationController::class, 'readAll'])->name('read-all');
});
