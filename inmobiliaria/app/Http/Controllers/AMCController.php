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
