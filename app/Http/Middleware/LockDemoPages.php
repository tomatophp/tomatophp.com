<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * In the public demo, pages that store secrets (SMTP, webhooks, API keys) are not reachable:
 * visitors are sent back to the dashboard with an explanation instead.
 */
class LockDemoPages
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        $page = $request->route()?->getControllerClass();

        if ($page && in_array(ltrim($page, '\\'), array_map(fn (string $class): string => ltrim($class, '\\'), config('demo.locked_pages', [])), true)) {
            Notification::make()
                ->title(__('Locked in the public demo'))
                ->body(__('This page stores credentials such as API keys or mail passwords, so it is disabled on demo.tomatophp.com. Install the plugin to try it.'))
                ->warning()
                ->send();

            return redirect(Filament::getCurrentOrDefaultPanel()->getUrl());
        }

        return $next($request);
    }
}
