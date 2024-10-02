<?php

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('test', function (){
    $token = "ya29.a0AcM612wcApY_dtW85Lyqz8n8SuXZ3JgaIasGKfCkhAbVXHf00uxNBIoe6koMCOPeoILl768Vs__D_e1csiyoHRz-CJc4uacJHcRG7lCpmLzQpAfdWskcCZHcpHuj0QFc4texMl5fg76OJLvjzcO2SstDzJdEPuxkCQaCgYKAWQSARMSFQHGX2Mias3da1KHXEDs-xydqk0Qww0169";

    return response()->json([
       "data" =>  $sites = \TomatoPHP\FilamentSeo\Facades\FilamentSeo::google()->setAccessToken($token)->listSites()
   ]);
});


Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::middleware(['web', 'throttle:10'])->group(function (){
        Route::get('/login/{provider}', [\App\Http\Controllers\AuthController::class, 'provider'])->name('login.provider');
        Route::get('/login/{provider}/callback', [\App\Http\Controllers\AuthController::class, 'callback'])->name('login.provider.callback');
    });

    Route::as('pwa.')->group(function()
    {
        Route::get('/manifest.json', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'index'])->name('manifest');
        Route::get('/offline/', [\TomatoPHP\FilamentPWA\Http\Controllers\PWAController::class, 'offline'])->name('offline');
    });
});
