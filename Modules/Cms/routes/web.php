<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\CmsController;

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

Route::domain(config('app.domain'))->middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->to('/' . app()->getLocale());
    })->middleware(['web'])->name('index');

    Route::prefix('ar')->middleware(['web', \Modules\Cms\Http\Middlewares\LangRoute::class])->group(function () {
        Route::name('ar.auth.')->group(function () {
            Route::get('/login', [\Modules\Cms\Http\Controllers\AuthController::class, 'login'])->name('login');
            Route::get('/register', [\Modules\Cms\Http\Controllers\AuthController::class, 'register'])->name('register');
            Route::get('/verify-otp', [\Modules\Cms\Http\Controllers\AuthController::class, 'otp'])->name('otp');
        });

        Route::name('ar.home.')->group(function () {
            Route::get('/', [\Modules\Cms\Http\Controllers\HomeController::class, 'index'])->name('index');
            Route::get('/open-source', [\Modules\Cms\Http\Controllers\HomeController::class, 'openSource'])->name('open-source');
            Route::get('/open-source/{docs}', [\Modules\Cms\Http\Controllers\HomeController::class, 'docs'])->name('open-source-docs');
            Route::get('/contact', [\Modules\Cms\Http\Controllers\HomeController::class, 'contact'])->name('contact');
            Route::get('/issues', [\Modules\Cms\Http\Controllers\HomeController::class, 'issues'])->name('issues');
            Route::get('/about', [\Modules\Cms\Http\Controllers\HomeController::class, 'about'])->name('about');
            Route::get('/donate', [\Modules\Cms\Http\Controllers\HomeController::class, 'donate'])->name('donate');
            Route::get('/blog', [\Modules\Cms\Http\Controllers\HomeController::class, 'blog'])->name('blog');
            Route::get('/blog/{post}', [\Modules\Cms\Http\Controllers\HomeController::class, 'post'])->name('post');
//            Route::get('/portfolios', [\Modules\Cms\Http\Controllers\HomeController::class, 'portfolios'])->name('portfolios');
//            Route::get('/portfolios/{portfolio}', [\Modules\Cms\Http\Controllers\HomeController::class, 'portfolio'])->name('portfolio');
//            Route::get('/services', [\Modules\Cms\Http\Controllers\HomeController::class, 'services'])->name('services');
//            Route::get('/services/{service}', [\Modules\Cms\Http\Controllers\HomeController::class, 'service'])->name('service');
            Route::get('/{username}', [\Modules\Cms\Http\Controllers\ProfileController::class, 'index'])->name('profile');
//            Route::get('/{page}', [\Modules\Cms\Http\Controllers\HomeController::class, 'page'])->name('page');
        });

    });

    Route::prefix('en')->middleware(['web', \Modules\Cms\Http\Middlewares\LangRoute::class])->group(function () {
        Route::name('en.auth.')->group(function () {
            Route::get('/login', [\Modules\Cms\Http\Controllers\AuthController::class, 'login'])->name('login');
            Route::get('/register', [\Modules\Cms\Http\Controllers\AuthController::class, 'register'])->name('register');
            Route::get('/verify-otp', [\Modules\Cms\Http\Controllers\AuthController::class, 'otp'])->name('otp');
        });

        Route::name('en.home.')->group(function () {
            Route::get('/', [\Modules\Cms\Http\Controllers\HomeController::class, 'index'])->name('index');
            Route::get('/open-source', [\Modules\Cms\Http\Controllers\HomeController::class, 'openSource'])->name('open-source');
            Route::get('/open-source/{docs}', [\Modules\Cms\Http\Controllers\HomeController::class, 'docs'])->name('open-source-docs');
            Route::get('/contact', [\Modules\Cms\Http\Controllers\HomeController::class, 'contact'])->name('contact');
            Route::get('/issues', [\Modules\Cms\Http\Controllers\HomeController::class, 'issues'])->name('issues');
            Route::get('/about', [\Modules\Cms\Http\Controllers\HomeController::class, 'about'])->name('about');
            Route::get('/donate', [\Modules\Cms\Http\Controllers\HomeController::class, 'donate'])->name('donate');
            Route::get('/blog', [\Modules\Cms\Http\Controllers\HomeController::class, 'blog'])->name('blog');
            Route::get('/blog/{post}', [\Modules\Cms\Http\Controllers\HomeController::class, 'post'])->name('post');
//            Route::get('/portfolios', [\Modules\Cms\Http\Controllers\HomeController::class, 'portfolios'])->name('portfolios');
//            Route::get('/portfolios/{portfolio}', [\Modules\Cms\Http\Controllers\HomeController::class, 'portfolio'])->name('portfolio');
//            Route::get('/services', [\Modules\Cms\Http\Controllers\HomeController::class, 'services'])->name('services');
//            Route::get('/services/{service}', [\Modules\Cms\Http\Controllers\HomeController::class, 'service'])->name('service');
            Route::get('/{username}', [\Modules\Cms\Http\Controllers\ProfileController::class, 'index'])->name('profile');
//            Route::get('/{page}', [\Modules\Cms\Http\Controllers\HomeController::class, 'page'])->name('page');
        });

    });
});
