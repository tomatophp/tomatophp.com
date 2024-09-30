<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(tenancy()){
            if(tenancy()->tenant){
                if(tenancy()->tenant->is_active){
                    return $next($request);
                }
                else {
                    abort(503, "Tenant is not active");
                }
            }
        }
    }
}
