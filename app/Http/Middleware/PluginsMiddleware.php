<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use TomatoPHP\FilamentNotes\FilamentNotesPlugin;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class PluginsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return $next($request);
        }

//        $plugins = [];
//        if(in_array( 'filament-users', auth()->user()->packages)){
//            $plugins[] = FilamentUsersPlugin::make();
//        }
//        if(in_array( 'filament-notes', auth()->user()->packages)){
//            $plugins[] = FilamentNotesPlugin::make();
//        }
//        filament()->getCurrentPanel()->plugins($plugins);

        return $next($request);
    }
}
