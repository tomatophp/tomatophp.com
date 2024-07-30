<?php

use Illuminate\Support\Facades\Route;


Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::get('/', \App\Livewire\RegisterDemo::class)->name('home');
    Route::get('/otp', \App\Livewire\RegisterOtp::class)->name('otp');
});
