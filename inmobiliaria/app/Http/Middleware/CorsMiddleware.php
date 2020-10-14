<?php

/**
* Location: /app/Http/Middleware
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CorsMiddleware
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
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, api_key'
        ];

        if ($request->isMethod('OPTIONS'))
        {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }

        $user = Auth::guard('api')->user();
        if($user){
            $req_token = $request->bearerToken();
            $cur_time = round(microtime(true)*1000);
            $sleep = $cur_time - intval($user->last_activity);
            $expire = intval(env('SESSION_EXPIRE', '1')) * 60 * 1000;

            if($user->access_token !== $req_token || $sleep > $expire){
                return response()->json([
                    'error' => 'Sesión no valido',
                    'function' => 'logout',
                    'redirect' => '/'
                ], 401);
            }

            $user->last_activity = $cur_time;
            $user->save();
        }

        $response = $next($request);
        foreach($headers as $key => $value)
        {
            $response->header($key, $value);
        }

        return $response;
    }
}