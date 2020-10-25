<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuscripcionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->storage_path = base_path().'/../../appservice';
        // $this->storage_path = base_path().'/public';
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    public function index(Request $request)
    {
        
    }

    public function getSuscripcionTypes(Request $request){
        $userId = $this->authUser()->IdUsuario;

        $history = DB::table('suscripcion_usuario')
                ->where('IdUsuario', $userId)
                ->count();

        $items = DB::table('suscripcioncabecera as s')
            ->join('tiposuscripcion as t', 's.IdTipoSuscripcion', 't.IdTipoSuscripcion')
            ->where('t.Estado', '1')
            ->select(
                's.*',
                't.Descripcion as Tipo'
            )->get();
        
        $cursubscription = DB::table('suscripcion_usuario')
                            ->where('IdUsuario', $userId)
                            ->where('Flg_Estado', '1')
                            ->orWhere('Flg_Estado', '-1')
                            ->select(
                                'IdSuscripcion',
                                'Fec_FechaFin'
                            )->first();
        

        return response()->json([
            'items' => $items,
            'current' => $cursubscription,
            'history' => $history
        ]);
    }

    public function createSuscripcion(Request $request){
        $userId = $this->authUser()->IdUsuario;

        $this->validate($request, [
            'promotion_id' => 'required',
            'document' => 'required',
            'document.*' => 'mimes:pdf,jpg,jpeg,png'
        ]);

        if($request->hasfile('document')){
            $doc = $request->file('document');
            $docname = time().'.'.$doc->extension();
            $doc->move($this->storage_path.'/documento', $docname);
            $des_documentUrl = "/documento/".$docname;
        }
        
        DB::table('suscripcion_usuario')
            ->where('IdUsuario', $userId)
            ->where('Flg_Estado', '1')
            ->orWhere('Flg_Estado', '-1')
            ->update(['Flg_Estado' => '0']);

        $promoId = $request->input('promotion_id');
        $selected = DB::table('suscripcioncabecera')
                        ->where('IdSuscripcion', $promoId)
                        ->first();

        if(!$selected){
            return response()->json([
                'status' => 'fail',
                'message' => 'invalid suscripcion',
            ], 400);
        }
        
        $sDate = date("Y-m-d H:i:s");
        $eDate = date("Y-m-d H:i:s", strtotime("+1 month"));

        $id = DB::table('suscripcion_usuario')->max('IdSuscripcionUsuario');
        $id=str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

        $suscription = array(
            'IdSuscripcion' => $promoId,
            'IdUsuario' => $userId,
            'IdSuscripcionUsuario' => $id,
            'IdTipoSuscripcion' => $selected->IdTipoSuscripcion,
            'Fec_FechaInicio' => $sDate,
            'Fec_FechaFin' => $eDate,
            'Flg_Estado' => '-1',
            'Num_MontoPago' => $selected->Num_Costo,
            'Num_Descuento' => '0',
            'Des_TipoTarjetaPago' => 'NINGUNA',
            'FechaCreacion' => $sDate
        );

        DB::table('suscripcion_usuario')->insert($suscription);

        return response()->json([
            'status' => 'success',
            'message' => 'Su solicitud se ingresó correctamente, espere la confirmación del administrador.'
        ]);
    }

    public function trialSuscripcion(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $history = DB::table('suscripcion_usuario')
                    ->where('IdUsuario', $userId)
                    ->count();

        if($history > 0){
            return response()->json([
                'status' => 'fail',
                'message' => 'you already passed through trial',
            ], 403);
        }
        
        $sDate = date("Y-m-d H:i:s");
        $eDate = date("Y-m-d H:i:s", strtotime("+1 month"));

        $id = DB::table('suscripcion_usuario')->max('IdSuscripcionUsuario');
        $id=str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

        $suscription = array(
            'IdSuscripcion' => '1',
            'IdUsuario' => $userId,
            'IdSuscripcionUsuario' => $id,
            'IdTipoSuscripcion' => '2',
            'Fec_FechaInicio' => $sDate,
            'Fec_FechaFin' => $eDate,
            'Flg_Estado' => '1',
            'Num_MontoPago' => '0',
            'Num_Descuento' => '0',
            'Des_TipoTarjetaPago' => 'NINGUNA',
            'FechaCreacion' => $sDate
        );

        DB::table('suscripcion_usuario')->insert($suscription);

        return response()->json([
            'status' => 'success',
            'message' => 'Bienvenido a la plataforma wlinni  , le quedan 60 dias de prueba  en la aplicación. Favor vuelva a ingresar.'
        ]);
    }
}
