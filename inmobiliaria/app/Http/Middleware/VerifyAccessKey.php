<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAccessKey
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
        $key = $request->headers->get('api_key');            
            
        if (isset($key) == env('APP_KEY')) {
            return $next($request);
        }
        else {
            return response()->json(['error' => 'Acceso no autorizado' ], 401);
        }
    }
}
