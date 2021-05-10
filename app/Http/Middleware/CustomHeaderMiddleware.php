<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomHeaderMiddleware
{
    /**
     * Handle an incoming request.
     * show out powerby,ratelimit-limit,rateremaining and application name
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,$headerName = 'X-name',$headeValue = 'API')
    {
        $response = $next($request);

        $response->headers->set($headerName,$headeValue);

        return $response;
    }
}
