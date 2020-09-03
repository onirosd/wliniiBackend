<?php

/**
* Location: /app/Http/Middleware
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SusAuthMiddleware
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
        $userId = Auth::guard('api')->user()->IdUsuario;
        $userSubscription = DB::table('suscripcion_usuario')
                            ->where('IdUsuario', $userId)
                            ->where('Flg_Estado', '1')
                            ->first();
        
        if(!$userSubscription){
            $penddingSubscription = DB::table('suscripcion_usuario')
                            ->where('IdUsuario', $userId)
                            ->where('Flg_Estado', '-1')
                            ->first();
            if($penddingSubscription){
                $msg = "Tu suscripción está pendiente";
                $redirectUrl = "/";
            }else{
                $msg = "No tienes suscripción";
                $redirectUrl = "/precios";
            }
            return response()->json([
                'status' => 'fail',
                'message' => $msg,
                'redirect' => $redirectUrl
            ], 403);
        }

        $expire = strtotime($userSubscription->Fec_FechaFin);
        $now = strtotime('now');
        $differ = $now - $expire;

        if($differ > 0){
            return response()->json([
                'status' => 'fail',
                'message' => 'Su suscripción ha expirado',
                'redirect' => "/precios"
            ], 403);
        }

        return $next($request);
    }
}