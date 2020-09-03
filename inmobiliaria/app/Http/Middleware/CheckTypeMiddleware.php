<?php

/**
* Location: /app/Http/Middleware
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        $userType = trim(Auth::guard('api')->user()->Flg_TipoUsuario);

        if(!$userType){
            return response()->json([
                'error' => 'No puedes acceder al sistema',
                'redirect' => "/"
            ], 401);
        }else if($type === 'agente' && $userType !== '1' && $userType !== '2'){
            return response()->json([
                'error' => 'No tienes acceso.',
                'redirect' => "/"
            ], 401);
        }else if($type === 'broker' && $userType !== '3'){
            return response()->json([
                'error' => 'No tienes acceso.',
                'redirect' => "/"
            ], 401);
        }
        // else if($type === 'nobroker' && $userType == '3'){
        //     return response()->json([
        //         'error' => 'No tienes acceso.',
        //         'redirect' => "/"
        //     ], 401);
        // }
        
        return $next($request);
    }
}