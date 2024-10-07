<?php

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::as('pwa.')->group(function()
    {
        Route::get('/manifest.json', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'index'])->name('manifest');
        Route::get('/offline/', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'offline'])->name('offline');
    });
});
