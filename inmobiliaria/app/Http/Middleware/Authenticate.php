<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return response()->json([
                'status' => 'fail',
                'error' => 'Acceso no autorizado.',
                'redirect' => "/login"
            ], 401);
        }

        $user = $this->auth->guard('api')->user();
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

        return $next($request);
    }
}
