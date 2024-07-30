<?php

use App\Providers\TenancyServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\HomeTheme\Http\Controllers\HomeThemeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([
    'web',
    'universal',
    TenancyServiceProvider::TENANCY_IDENTIFICATION,
])->group(function () {
    Route::get('/', [HomeThemeController::class, 'index']);

    Route::get('/login/url', [\App\Http\Controllers\LoginUrl::class, 'index']);
});
