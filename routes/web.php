<?php

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::get('/', \App\Livewire\RegisterDemo::class)->name('home');
    Route::get('/verify-otp', \App\Livewire\RegisterOtp::class)->name('verify.otp');
});
