<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\TipoInmueble;
use App\Models\EstadoPublicacion;
use App\Models\PublicacionCabecera;
use App\Models\PublicacionDetalle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AMCController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    public function index(Request $request)
    {
        $userId = $this->authUser()->IdUsuario;
        if(isset($_GET['sold'])) $estado = 3;
        else $estado = 1;

        $publications = PublicacionCabecera::query()
            ->join('tipomoneda as moneda', 'moneda.IdTipoMoneda', 'publicacioncabecera.IdTipoMoneda')
            ->where(function($q){
                $q->select('Id_EstadoPublicacion')
                        ->from('publicaciondetalleestados')
                        ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                        ->where('Flg_Activo', 1)
                        ->limit(1);
            }, $estado)
            ->select(
                'publicacioncabecera.IdPubCabecera',
                'publicacioncabecera.Des_Titulo',
                'publicacioncabecera.IdUbigeo',
                'publicacioncabecera.Des_Urbanizacion',
                'publicacioncabecera.Num_AreaTotal',
                'publicacioncabecera.Num_AreaTechado',
                'publicacioncabecera.Num_Habitaciones',
                'publicacioncabecera.Num_Cochera',
                'publicacioncabecera.IdTipoComision',
                'publicacioncabecera.Num_Comision',
                'publicacioncabecera.Num_ComisionCompartir',
                'publicacioncabecera.IdTipoMoneda',
                'publicacioncabecera.IdTipoOperacion',
                'publicacioncabecera.IdTipoInmueble',
                'publicacioncabecera.Num_Precio',
                'publicacioncabecera.Flg_Consultar',
                'publicacioncabecera.Flg_MostrarDireccion',
                'publicacioncabecera.Des_Coordenadas',
                'publicacioncabecera.Des_DireccionManual',
                'publicacioncabecera.FechaCreacion'
            )->with(['images', 'detail' => function($q){
                $q->where('Flg_Activo', 1);
            }]);
        
            
        if(isset($_GET['IdTipoMoneda']) && isset($_GET['Num_Precio'])) {
            $_range =  $_GET['Num_Precio'];
            $moneda = DB::table('tipomoneda')
                ->where("IdTipoMoneda", $_GET['IdTipoMoneda'])
                ->select("Moneda")
                ->first();

            $publications->whereRaw("(Num_Precio * $moneda->Moneda / moneda.Moneda) >= $_range[0] AND (Num_Precio * $moneda->Moneda / moneda.Moneda) <= $_range[1]");
        }

        if(isset($_GET['IdUbigeo'])) $publications->where('publicacioncabecera.IdUbigeo', $_GET['IdUbigeo']);
        if(isset($_GET['IdTipoOperacion'])) $publications->where('publicacioncabecera.IdTipoOperacion', $_GET['IdTipoOperacion']); 
        if(isset($_GET['IdTipoInmueble'])) $publications->where('publicacioncabecera.IdTipoInmueble', $_GET['IdTipoInmueble']); 

        if(isset($_GET['Num_Habitaciones'])) {
            $_count =  $_GET['Num_Habitaciones'];
            if($_count >= 5 ) $publications->where('publicacioncabecera.Num_Habitaciones', '>=' , 5); 
            else $publications->where('publicacioncabecera.Num_Habitaciones', $_count); 
        }

        if(isset($_GET['Num_Banios'])) {
            $_count =  $_GET['Num_Banios'];
            if($_count >= 5 ) $publications->where('publicacioncabecera.Num_Banios', '>=' , 5); 
            else $publications->where('publicacioncabecera.Num_Banios', $_count); 
        }

        if(isset($_GET['Num_Cochera'])) {
            $_count =  $_GET['Num_Cochera'];
            if($_count >= 5 ) $publications->where('publicacioncabecera.Num_Cochera', '>=' , 5); 
            else $publications->where('publicacioncabecera.Num_Cochera', $_count); 
        }

        if(isset($_GET['Num_AreaTechado'])) {
            $_range =  $_GET['Num_AreaTechado'];
            $publications->where('publicacioncabecera.Num_AreaTechado', '>=', $_range[0]);
            $publications->where('publicacioncabecera.Num_AreaTechado', '<=', $_range[1]); 
        }

        if(isset($_GET['Num_AreaTotal'])) {
            $_range =  $_GET['Num_AreaTotal'];
            $publications->where('publicacioncabecera.Num_AreaTotal', '>=', $_range[0]);
            $publications->where('publicacioncabecera.Num_AreaTotal', '<=', $_range[1]); 
        }

        // if(isset($_GET['Num_Precio'])) {
        //     $_range =  $_GET['Num_Precio'];
        //     $publications->where('calculated_price', '>=', $_range[0]);
        //     $publications->where('calculated_price', '<=', $_range[1]); 
        // }

        if(isset($_GET['FechaCreacion'])) {
            $_days = (string) $_GET['FechaCreacion'];
            if($_days === 'today') $date = date('Y-m-d')." 00:00:00";
            else $date = date('Y-m-d', strtotime($_days))." 00:00:00";
            $publications->where('publicacioncabecera.FechaCreacion', '>=', $date); 
        }

        $count = $publications->count();
        $data = $publications->get();
        
        return response()->json([
            'publications' => $data,
            'total' => $count
        ]);
    }

    public function _index_back(Request $request)
    {
        $userId = $this->authUser()->IdUsuario;
        $query = PublicacionCabecera::query();//->where('IdUsuario', $userId);

        if(isset($_GET['IdUbigeo'])) $query->where('IdUbigeo', $_GET['IdUbigeo']);
        if(isset($_GET['IdTipoOperacion'])) $query->where('IdTipoOperacion', $_GET['IdTipoOperacion']); 
        if(isset($_GET['IdTipoInmueble'])) $query->where('IdTipoInmueble', $_GET['IdTipoInmueble']); 
        if(isset($_GET['IdTipoMoneda'])) $query->where('IdTipoMoneda', $_GET['IdTipoMoneda']); 

        if(isset($_GET['Num_Habitaciones'])) {
            $_count =  $_GET['Num_Habitaciones'];
            if($_count >= 5 ) $query->where('Num_Habitaciones', '>=' , 5); 
            else $query->where('Num_Habitaciones', $_count); 
        }

        if(isset($_GET['Num_Banios'])) {
            $_count =  $_GET['Num_Banios'];
            if($_count >= 5 ) $query->where('Num_Banios', '>=' , 5); 
            else $query->where('Num_Banios', $_count); 
        }

        if(isset($_GET['Num_Cochera'])) {
            $_count =  $_GET['Num_Cochera'];
            if($_count >= 5 ) $query->where('Num_Cochera', '>=' , 5); 
            else $query->where('Num_Cochera', $_count); 
        }

        if(isset($_GET['Num_AreaTechado'])) {
            $_range =  $_GET['Num_AreaTechado'];
            $query->where('Num_AreaTechado', '>=', $_range[0]);
            $query->where('Num_AreaTechado', '<=', $_range[1]); 
        }

        if(isset($_GET['Num_AreaTotal'])) {
            $_range =  $_GET['Num_AreaTotal'];
            $query->where('Num_AreaTotal', '>=', $_range[0]);
            $query->where('Num_AreaTotal', '<=', $_range[1]); 
        }

        if(isset($_GET['Num_Precio'])) {
            $_range =  $_GET['Num_Precio'];
            $query->where('Num_Precio', '>=', $_range[0]);
            $query->where('Num_Precio', '<=', $_range[1]); 
        }

        if(isset($_GET['FechaCreacion'])) {
            $_days = (string) $_GET['FechaCreacion'];
            if($_days === 'today') $date = date('Y-m-d')." 00:00:00";
            else $date = date('Y-m-d', strtotime($_days))." 00:00:00";
            $query->where('FechaCreacion', '>=', $date); 
        }

        if(isset($_GET['sold'])) $estado = 3;
        else $estado = 1;

        $query->where(function($_query){
            $_query->select('Id_EstadoPublicacion')
                    ->from('publicaciondetalleestados')
                    ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                    ->where('Flg_Activo', 1)
                    ->limit(1);
        }, $estado);

        $publications = $query->select(
            'IdPubCabecera',
            'Des_Titulo',
            'IdUbigeo',
            'Des_Urbanizacion',
            'Num_AreaTotal',
            'Num_AreaTechado',
            'Num_Habitaciones',
            'Num_Cochera',
            'IdTipoComision',
            'Num_Comision',
            'Num_ComisionCompartir',
            'IdTipoMoneda',
            'IdTipoOperacion',
            'IdTipoInmueble',
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with(['images', 'detail' => function($q){
                $q->where('Flg_Activo', 1);
            }]);

        $count = $publications->count();
        $data = $publications->get();
        
        return response()->json([
            'publications' => $data,
            'total' => $count
        ]);
    }
    
}
