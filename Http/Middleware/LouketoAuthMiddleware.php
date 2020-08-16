<?php

namespace Modules\LouketoAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LouketoAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
