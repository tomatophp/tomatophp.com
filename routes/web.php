<?php

use Illuminate\Support\Facades\Route;


Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});
