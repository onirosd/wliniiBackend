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

class InmuebleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    public function index(Request $request)
    {
        $query = PublicacionCabecera::query();

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

        if(isset($_GET['type'])) {
            $_type = $_GET['type'];
            $cur_date = date('Y-m-d H:i:s').'.000';

            if($_type === 'featured'){
                $query->whereExists(function($q) use ($cur_date) {
                    $q->select('IdPubCabecera')
                            ->from('publicaciondestacada')
                            ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                            ->where('FechaInicio', '<', $cur_date)
                            ->where('FechaFin', '>', $cur_date);
                });
            }
            // else if($_type === 'latest'){
            //     $query->orderBy('FechaCreacion', 'DESC');
            // }
        }

        $query->where(function($_query){
            $_query->select('Id_EstadoPublicacion')
                    ->from('publicaciondetalleestados')
                    ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                    ->where('Flg_Activo', 1)
                    ->limit(1);
        }, "1");

        $publications = $query->select(
            'IdPubCabecera',
            'Des_Titulo',
            'IdUbigeo',
            'Des_Urbanizacion',
            'Num_AreaTotal',
            'Num_Habitaciones',
            'Num_Cochera',
            'IdTipoComision',
            'Num_Comision',
            'Num_ComisionCompartir',
            'IdTipoMoneda',
            'IdTipoOperacion',
            'Num_Banios',
            'Num_BaniosVisita',
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with('images')->orderBy('FechaCreacion', 'DESC');

        $count = $publications->count();

        if(isset($_GET['count'])){
            $countPerPage = $_GET['count'];
            $offset = ( intval($_GET['page']) - 1) * $countPerPage;
            $data = $publications->offset($offset)->limit($countPerPage)->get();
        }else{
            $data = $publications->get();
        }
        
        return response()->json([
            'publications' => $data,
            'total' => $count
        ]);
    }

    public function featured($personid = null)
    {
        $count = 6;
        $cur_date = date('Y-m-d H:i:s').'.000';

        $query = PublicacionCabecera::query();

        if($personid){
            $selectedUser = Usuario::where('IdPersonal', $personid)->first();
            if($selectedUser){
                $query->where('IdUsuario', $selectedUser->IdUsuario);
            }else{
                return response()->json([
                    'status' => 'fail',
                    'message' => "user is not found out"
                ]);
            };
        };

        $query->where(function($_query){
            $_query->select('Id_EstadoPublicacion')
                    ->from('publicaciondetalleestados')
                    ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                    ->where('Flg_Activo', 1)
                    ->limit(1);
        }, "1");

        $query->whereExists(function($query) use ($cur_date) {
            $query->select('IdPubCabecera')
                    ->from('publicaciondestacada')
                    ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                    ->where('FechaInicio', '<', $cur_date)
                    ->where('FechaFin', '>', $cur_date);
        });

        $publications = $query->select(
            'IdPubCabecera',
            'Des_Titulo',
            'IdUbigeo',
            'Des_Urbanizacion',
            'Num_AreaTotal',
            'Num_Habitaciones',
            'Num_Cochera',
            'IdTipoComision',
            'Num_Comision',
            'Num_ComisionCompartir',
            'IdTipoMoneda',
            'IdTipoOperacion',
            'Num_Banios',
            'Num_BaniosVisita',
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with('images')->orderBy('FechaCreacion', 'DESC')->limit($count)->get();

        return response()->json($publications);
    }

    public function latest($personid = null)
    {
        $count = 6;
        $cur_date = date('Y-m-d H:i:s').'.000';

        $query = PublicacionCabecera::query();

        if($personid){
            $selectedUser = Usuario::where('IdPersonal', $personid)->first();
            if($selectedUser){
                $query->where('IdUsuario', $selectedUser->IdUsuario);
            }else{
                return response()->json([
                    'status' => 'fail',
                    'message' => "user is not found out"
                ]);
            };
        };

        $query->where(function($_query){
            $_query->select('Id_EstadoPublicacion')
                    ->from('publicaciondetalleestados')
                    ->whereColumn('IdPubCabecera', 'publicacioncabecera.IdPubCabecera')
                    ->where('Flg_Activo', 1)
                    ->limit(1);
        }, "1");

        $publications = $query->select(
            'IdPubCabecera',
            'Des_Titulo',
            'IdUbigeo',
            'Des_Urbanizacion',
            'Num_AreaTotal',
            'Num_Habitaciones',
            'Num_Cochera',
            'IdTipoComision',
            'Num_Comision',
            'Num_ComisionCompartir',
            'IdTipoMoneda',
            'IdTipoOperacion',
            'Num_Banios',
            'Num_BaniosVisita',
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with('images')->orderBy('FechaCreacion', 'DESC')-> limit($count)->get();

        return response()->json($publications);
    }

    public function myfeatured(Request $request)
    {
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        return redirect()->route('featured', ['personid' => $personId]);
    }

    public function mylatest(Request $request)
    {
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        return redirect()->route('latest', ['personid' => $personId]);
    }
}
