<?php

use App\Http\Controllers\AuthController;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::get('/', \App\Livewire\RegisterDemo::class)->name('home');
    Route::get('/verify-otp', \App\Livewire\RegisterOtp::class)->name('verify.otp');

    Route::middleware(['web', 'throttle:10'])->group(function (){
        Route::get('/login/{provider}', [AuthController::class, 'provider'])->name('login.provider');
        Route::get('/login/{provider}/callback', [AuthController::class, 'callback'])->name('login.provider.callback');
    });

    Route::as('pwa.')->group(function()
    {
        Route::get('/manifest.json', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'index'])->name('manifest');
        Route::get('/offline/', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'offline'])->name('offline');
    });
});
