<?php

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::middleware(['web', 'throttle:10'])->group(function (){
        Route::get('/login/{provider}', [\TomatoPHP\FilamentTenancy\Http\Controllers\AuthController::class, 'provider'])->name('login.provider');
        Route::get('/login/{provider}/callback', [\TomatoPHP\FilamentTenancy\Http\Controllers\AuthController::class, 'callback'])->name('login.provider.callback');
    });

    Route::as('pwa.')->group(function()
    {
        Route::get('/manifest.json', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'index'])->name('manifest');
        Route::get('/offline/', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'offline'])->name('offline');
    });
});
