<?php

namespace App\Http\Middleware;

use App\Events\AccessEvents;
use Closure;

class AccessLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        event(new AccessEvents($request , $response));
        return $response;
    }
}
