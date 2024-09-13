<?php

namespace Modules\Cms\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;


class LangRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $checkLocationAR = Str::of($request->url())->contains('/ar');
        $checkLocationEN = Str::of($request->url())->contains('/en');
        if($checkLocationAR){
            app()->setLocale('ar');
            if(!isset($_COOKIE['lang'])){
                setcookie('lang', json_encode([
                    'id' => 'ar',
                    'name' => 'Arabic'
                ]), time() + (86400 * 30), "/");
            }
        }
        if($checkLocationEN){
            app()->setLocale('en');
            if(!isset($_COOKIE['lang'])){
                setcookie('lang', json_encode([
                    'id' => 'en',
                    'name' => 'English'
                ]), time() + (86400 * 30), "/");
            }
        }

        return $next($request);
    }
}
