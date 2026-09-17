<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * In the public demo, pages that store secrets (SMTP, webhooks, API keys) or write files to the
 * server are not reachable: visitors are sent back to the dashboard with an explanation instead.
 * Pages are matched by class (`demo.locked_pages`) or by panel path (`demo.locked_paths`).
 */
class LockDemoPages
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        $page = $request->route()?->getControllerClass();

        $isLockedPage = $page && in_array(
            ltrim($page, '\\'),
            array_map(fn (string $class): string => ltrim($class, '\\'), config('demo.locked_pages', [])),
            true,
        );

        if ($isLockedPage || $request->is(...config('demo.locked_paths', []))) {
            Notification::make()
                ->title(__('Locked in the public demo'))
                ->body(__('This page stores credentials such as API keys or mail passwords, or writes files to the server, so it is disabled on demo.tomatophp.com. Install the plugin to try it.'))
                ->warning()
                ->send();

            return redirect(Filament::getCurrentOrDefaultPanel()->getUrl());
        }

        return $next($request);
    }
}
