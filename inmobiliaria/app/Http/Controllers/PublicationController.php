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
use Illuminate\Support\Facades\File;

class PublicationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->storage_path = base_path().'/public';
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    public function index(Request $request)
    {
        $userId = $this->authUser()->IdUsuario;
        $query = PublicacionCabecera::query()->where('IdUsuario', $userId);

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
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with(['images', 'detail' => function($q){
                $q->where('Flg_Activo', 1);
            }])->orderBy('FechaCreacion', 'DESC');

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

    public function getPublicationsByBroker(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $affiliated = DB::table('PersonaRelacion_Hist as a')
                    ->join('Usuario as u', 'a.IdPersonal',  '=', 'u.IdPersonal')
                    ->select(
                        'u.IdUsuario'
                    )->where([
                        ['a.IdPersonalPadre', $personId],
                        ['a.Flg_EstadoAfiliado', 1]
                    ])->get();
        
        foreach ($affiliated as $_affiliatedUser) {
            $related_list[] = $_affiliatedUser->IdUsuario;
        }

        $query = PublicacionCabecera::query()->whereIn('IdUsuario', $related_list);

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
            'Num_Precio',
            'Flg_Consultar',
            'Flg_MostrarDireccion',
            'Des_Coordenadas',
            'Des_DireccionManual',
            'FechaCreacion'
            )->with(['images', 'detail' => function($q){
                $q->where('Flg_Activo', 1);
            }])->orderBy('FechaCreacion', 'DESC');

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

    public function createPublication(Request $request){
        // $user = Auth::guard('api')->user();
        $userId = $this->authUser()->IdUsuario;
        $date = date("Y-m-d H:i:s");

        $data = $request->all();
        $id = PublicacionCabecera::max('IdPubCabecera');
        $id=str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

        $data['IdUsuario'] = $userId;
        $data['IdPubCabecera'] = $id;
        $data['FechaCreacion'] = $date;
        $data['FechaModificacion'] = $date;
        PublicacionCabecera::insert($data);

        $detailId = PublicacionDetalle::max('IdPubDetalle');
        $detailId=str_pad(intval($detailId)+1,8,"0",STR_PAD_LEFT);
        PublicacionDetalle::insert([
            'IdPubDetalle' => $detailId,
            'IdPubCabecera' => $id,
            'IdUsuario' => $userId,
            'Id_EstadoPublicacion' => 1,
            'Flg_Activo' => 1,
            'FechaCreacion' => $date
        ]);

        return json_encode(array('status' => 'success', 'message' => 'successfully created', 'id' => $id));
    }

    public function publicationByID(Request $request, $publication_id)
    {
        $userId = $this->authUser()->IdUsuario;
        $oldDetail = PublicacionDetalle::where('IdUsuario', $userId)
                                        ->where('IdPubCabecera', $publication_id)
                                        ->where('Flg_Activo', 1)
                                        ->with('state')
                                        ->first();
        if(!$oldDetail || $oldDetail->Id_EstadoPublicacion == 3 || $oldDetail->Id_EstadoPublicacion == 4){
            return response()->json([
                'status' => 'fail',
                'message' => 'La publicación ya fue cancelada'
            ], 404);
        }

        $publication = PublicacionCabecera::where([
            'IdPubCabecera' => $publication_id,
            'IdUsuario' => $userId
        ])->with('images')->first();

        return response()->json($publication);
    }

    public function publicationDetailByID(Request $request, $publication_id)
    {
        $publication = PublicacionCabecera::where([
            'IdPubCabecera' => $publication_id,
        ])->with(['images', 'detail' => function($q){
            $q->where('Flg_Activo', 1);
        }])->first();

        $tipocommision = DB::table('tipocomision')->where('IdTipoComision', $publication->IdTipoComision)->first();
        $tipoinmueble = DB::table('tipoinmueble')->where('IdTipoInmueble', $publication->IdTipoInmueble)->first();
        $tipomoneda = DB::table('tipomoneda')->where('IdTipoMoneda', $publication->IdTipoMoneda)->first();
        $tipooperacion = DB::table('tipooperacion')->where('IdTipoOperacion', $publication->IdTipoOperacion)->first();
        $ubigeo = DB::table('ubigeo')->where('IdUbigeo', $publication->IdUbigeo)->first();

        $ownerInfo = Usuario::query()
                    ->join('persona as p', 'usuario.IdPersonal', 'p.IdPersonal')
                    ->where('usuario.IdUsuario', $publication->IdUsuario)
                    ->select(
                        'p.Des_Correo1 as Correo',
                        'p.Des_Telefono1 as Phone'
                    )->first();

        $publication['tipocommision'] = $tipocommision;
        $publication['tipoinmueble'] = $tipoinmueble;
        $publication['tipomoneda'] = $tipomoneda;
        $publication['tipooperacion'] = $tipooperacion;
        $publication['ubigeo'] = $ubigeo;
        $publication['owner'] = $ownerInfo;

        return response()->json($publication);
    }

    public function updatePublication(Request $request, $publication_id)
    {
        $userId = $this->authUser()->IdUsuario;
        $data = $request->input('data');

        $id = PublicacionCabecera::where('IdPubCabecera', $publication_id)->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'updated successfully',
            'id' => $id
        ]);
    }

    public function addImages(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $id = $request->input('publication_id');

        $this->validate($request, [
            'filenames' => 'required',
            'filenames.*' => 'mimes:jpg,jpeg,png'
        ]);

        if($request->hasfile('filenames')){
            $num = 0;
            foreach($request->file('filenames') as $file){
                $num ++;
                $name = time().$num.'.'.$file->extension();
                $file->move($this->storage_path.'/images/publicacion/', $name);
                $data[] = ['IdPubCabecera' => $id, 'IdUsuario' => $userId, 'Des_url' => '/images/publicacion/'.$name ];
            }
        }

        DB::table('publicaciondetalleimagenes')->insert($data);
        return json_encode(array('status' => 'success', 'message' => 'successfully uploaded'));
    }

    public function removeImage(Request $request, $imageId){
        $userId = $this->authUser()->IdUsuario;
        try{

            $image = DB::table('publicaciondetalleimagenes')->where('IdPubImage', $imageId)->first();
            // $id = DB::table('publicaciondetalleimagenes')->where('IdPubImage', $imageId)->delete();
             
             //echo $image->Des_url; 

             if(File::exists($image->Des_url)) {
                File::delete($image->Des_url);
                echo"entrando con exito";
             }

 //if ( is_file($this->storage_path.$image->Des_url) ) {
   //chmod ($this->storage_path.$image->Des_url, 777 );
  // unlink ($this->storage_path.$image->Des_url);
//}

            //unlink($this->storage_path.$image->Des_url);
            //Storage::delete($this->storage_path.$image->Des_url);
        }catch(Throwable $e){
            return response()->json([
                'status' => 'fail',
                'message' => "can't remove the image"
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'A image was removed successfully'
        ]);
    }

    public function updatePublicacionDetalle(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $date = date("Y-m-d H:i:s");
        $data = $request->all();

        $oldDetail = PublicacionDetalle::where('IdUsuario', $userId)
                                        ->where('IdPubCabecera', $data['IdPubCabecera'])
                                        ->where('Flg_Activo', 1)
                                        ->first();

        if(!$oldDetail || in_array($oldDetail->Id_EstadoPublicacion, array(3, 4))){
            return response()->json([
                'status' => 'fail',
                'message' => "Can't update Publication Detalle"
            ]);
        };

        if($data['Id_EstadoPublicacion'] == 3 && !$data['IdUsuarioCompartido']){
            return response()->json([
                'status' => 'fail',
                'message' => "User was not selected"
            ]);
        }

        // update Publication detail
        if($oldDetail->Id_EstadoPublicacion === $data['Id_EstadoPublicacion']){
            PublicacionDetalle::where('IdPubDetalle', $oldDetail->IdPubDetalle)->update($data);
        }else{
            PublicacionDetalle::where('IdPubDetalle', $oldDetail->IdPubDetalle)->update(['Flg_Activo' => 0]);
        
            $id = PublicacionDetalle::max('IdPubDetalle');
            $id = str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

            $data['IdPubDetalle'] = $id;
            $data['IdUsuario'] = $userId;
            $data['FechaCreacion'] = $date;
            $data['Flg_Activo'] = 1;
            
            PublicacionDetalle::insert($data);
        };

        // Generate notifications
        if($data['Id_EstadoPublicacion'] == 3 ){
            $maxId = DB::table('Notificaciones')->max('IdNotificacion');
            $selectedUserId = $data['IdUsuarioCompartido'];

            $notiId=str_pad(intval($maxId) + 1, 8, "0", STR_PAD_LEFT);
            $notifications[] = array(
                'IdUsuario' => $userId,
                'IdNotificacion' => $notiId,
                'Flg_Tipo' => "1",
                'Flg_Estado' => "0",
                'Flg_Leer' => 0,
                'IdUsuarioRemitente' => $selectedUserId,
                'FechaCreacion' => $date
            );

            $notiId=str_pad(intval($maxId) + 2, 8, "0", STR_PAD_LEFT);
            $notifications[] = array(
                'IdUsuario' => $selectedUserId,
                'IdNotificacion' => $notiId,
                'Flg_Tipo' => "1",
                'Flg_Estado' => "0",
                'Flg_Leer' => 0,
                'IdUsuarioRemitente' => $userId,
                'FechaCreacion' => $date
            );

            DB::table('Notificaciones')->insert($notifications);
        }

        return json_encode(array('status' => 'success', 'message' => 'updated the state of Publicacion'));
    }
}
