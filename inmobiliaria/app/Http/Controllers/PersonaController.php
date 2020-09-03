<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Persona;
use App\Models\Agente;
use App\Models\ResumenAgente;
use App\Models\PersonaRelacionHist;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PersonaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->storage_path = base_path().'/public';
    }

    // 
    public function index()
    {
      //
    }

    private function authUser()
    {
        return Auth::guard('api')->user();
    }

    // // CREATE
    // public function crearUsuario(Request $request)
    // {
    //     if($request->isJson()){
    //         $data = $request->json()->all();
    //         $usuario = Usuario::create($data);
    //         return response()->json($usuario, 201);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }

    //READ ALL
    // public function mostrarUsuarios(Request $request)
    // {
    //     if($request->isJson()){
    //         $usuarios = Usuario::All();
    //         return response()->json($usuarios,200);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }
    // READ ONE
    public function mostrarPersona($id, Request $request)
    {
        if($request->isJson()){
            $usuario = Persona::findOrFail($id);
            return response()->json($usuario);
        }
        return response()->json(['error' => 'No autorizado', 'redirect' => '/login'],401);
    }

    public function mostrarPersonaHist($id, Request $request)
    {
        if($request->isJson()){
            $id=str_pad($id,8,"0",STR_PAD_LEFT);
            $usuario = Persona::findOrFail($id);
            $personaRelacionHist = PersonaRelacionHist::findOrFail($id);
            return response()->json(array($usuario,$personaRelacionHist));
        }
        return response()->json(['error' => 'No autorizado', 'redirect' => '/login'],401);
    }

    public function mostrarResumenAgente($id, Request $request)
    {
        if($request->isJson()){
            $id=str_pad($id,8,"0",STR_PAD_LEFT);
            $usuario = Persona::findOrFail($id);
            $resumenAgente = ResumenAgente::findOrFail($id);
            return response()->json(array($usuario,$resumenAgente));
        }
        return response()->json(['error' => 'No autorizado', 'redirect' => '/login'],401);
    }

    //UPDATE
    public function actualizarPersona($id, Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            $usuario = Persona::findOrFail($id);
            $usuario->update($data);
            return response()->json(['message'=>'Agente actualizado correctamente.'], 201);
        } 
    }
    // //DELETE
    // public function borrarUsuario($id, Request $request)
    // {
    //     if($request->isJson()){
    //         $usuario = Usuario::findOrFail($id);
    //         $usuario->delete();
    //         return response()->json(['msj' => 'Usuario borrado correctamente']);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }

    public function getPersonalInfo(Request $request){
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $person = Persona::findOrFail($personId);
        return response()->json($person);
    }

    public function updatePersonalInfo(Request $request){
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);
        $data = $request->json()->all();
        $data['Des_NombreCompleto'] = trim(trim($data['Des_PrimerNombre'])." ".trim($data['Des_SegundoNombre'])." ".trim($data['Des_ApePaterno'])." ".trim($data['Des_AperMaterno']));
      // print_r($data);
        $person = Persona::where("IdPersonal", $personId)
                           ->update($data);
        return response()->json(['message'=>'Agente actualizado correctamente.']);
    }

    public function getBrokerPersonalInfo(Request $request){
        $user = $this->authUser();
        $personId = str_pad($user->IdPersonal,8,"0",STR_PAD_LEFT);

        $person = Persona::findOrFail($personId);
        return response()->json($person);
    }

    public function updateBrokerPersonalInfo(Request $request){
        $user = $this->authUser();
        $personId = str_pad($user->IdPersonal,8,"0",STR_PAD_LEFT);
        $data = $request->json()->all();

        $data['Des_NombreCompleto'] = trim(trim($data['Des_PrimerNombre'])." ".trim($data['Des_SegundoNombre'])." ".trim($data['Des_ApePaterno'])." ".trim($data['Des_AperMaterno']));

        $person = Persona::where("IdPersonal", $personId)
                        ->update($data);
        return response()->json(['message'=>'Agente actualizado correctamente.']);
    }

    public function getPersonalNames(Request $request)
    {
        $personNames = DB::table('Usuario')
            ->join('Persona', 'Usuario.IdPersonal', 'Persona.IdPersonal')
            ->where('Usuario.Flg_TipoUsuario', 1)
            ->select(['Usuario.IdUsuario', 'Persona.Des_NombreCompleto'])->get();
        return response()->json($personNames);
    }

    public function getResumenAgentes(Request $request){
        $count = 8;
        $agentes = ResumenAgente::select(
            'Des_Numero',
            'Des_Email',
            'Num_Valoracion',
            'Num_Activas',
            'Des_NombreCompleto',
            'IdPersonal'
        )->with('persona')->orderBy('Num_Valoracion', 'desc')->limit($count)->get();

        return response()->json($agentes);
    }

    public function getResumenAgente(Request $request){
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $agente = $this->fetchResumenAgente($personId);
        return response()->json($agente);
    }

    public function getResumenAgenteById(Request $request, $personaId){
        $agente = $this->fetchResumenAgente($personaId);
        return response()->json($agente);
    }

    public function getResumenAgenteByPublication(Request $request, $id){
        $pubInfo = DB::table('publicacioncabecera as pub')
                        ->where('IdPubCabecera', $id)
                        ->join('usuario as u', 'u.IdUsuario', 'pub.IdUsuario')
                        ->select(
                            'u.IdPersonal'
                        )->first();
        if($pubInfo){
            $agente = $this->fetchResumenAgente($pubInfo->IdPersonal);
            return response()->json($agente);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'Agente not exist'
            ]);
        };
    }

    public function getResumenAgenteByBroker(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $agente = Persona::select(
            'Des_NombreCompleto',
            'Des_Telefono1',
            'Des_Correo1',
            'Des_ComentarioPersona',
            'Img_Personal',
            'Des_Ubicacion'
        )->findOrFail($personId);
        
        return response()->json($agente);
    }

    public function getRelatedPersonas(Request $request){
        $userId = $this->authUser()->IdUsuario;
        $personId = $this->authUser()->IdPersonal;
        $personId = str_pad($personId,8,"0",STR_PAD_LEFT);

        $affiliated = DB::table('PersonaRelacion_Hist as a')
                    ->join('Persona as b', 'a.IdPersonal',  '=', 'b.IdPersonal')
                    ->select(
                        'a.IdPersonal',
                        'b.Des_NombreCompleto as Nombre',
                        'b.Des_ComentarioPersona as Comentario',
                        'a.Flg_EstadoAfiliado as Estado',
                        'b.Img_Personal'
                    )->where([
                        ['a.IdPersonalPadre', $personId],
                        ['a.Flg_EstadoAfiliado', 1]
                    ])->get();
        
        $unAffiliated = DB::table('BDSolicitudes as a')
                    ->join('Persona as b', 'a.CodigoMVCSPadre', '=', 'b.CodigoMVCS')
                    ->select(
                        'a.IdbdSolicitudes',
                        DB::raw("a.PrimerNombre +' '+a.SegundoNombre +' '+ApellidoPaterno+' '+ApellidoMaterno as Nombre"),
                        "a.Mensaje as Comentario",
                        "a.Estado",
                        'b.Img_Personal'
                    )->where([
                        ['b.IdPersonal', $personId],
                        ['a.Estado', 0]
                    ])->get();

        return response()->json([
            'affiliated' => $affiliated,
            'unAffiliated' => $unAffiliated
            ]);
    }

    private function fetchResumenAgente($personaId){
        $agente = ResumenAgente::query()
                ->join('Persona', 'resumenagente.IdPersonal', 'Persona.IdPersonal')
                ->select(
                    'resumenagente.*',
                    'Persona.Des_Telefono1',
                    'Persona.Des_Correo1',
                    'Persona.Des_Rs_Facebook',
                    'Persona.Des_Rs_Twitter',
                    'Persona.Des_Rs_Linkedin',
                    'Persona.Img_Personal'
                )->where('resumenagente.IdPersonal', $personaId)->first();
        
        return $agente;
    }

    public function addProfileImage(Request $request){
        $personId = $this->authUser()->IdPersonal;

        $this->validate($request, [
            'file' => 'required',
            'file.*' => 'mimes:jpg,jpeg,png'
        ]);

        $file = $request->file('file');
        $fileName = time().'.'.$file->extension();
        $file->move($this->storage_path.'/images/perfil/', $fileName);

        $person = Persona::where("IdPersonal", $personId)->first();
        $oldImage = $person->Img_Personal;

        Persona::where("IdPersonal", $personId)->update(['Img_Personal' => '/images/perfil/'.$fileName]);
        
        if($oldImage){
            unlink($this->storage_path.$oldImage);
        }

        return response('/images/perfil/'.$fileName);
    }

    public function findAgenteByCode($code){
        $agente = Agente::where('CodigoMVCS', $code)
                ->select(
                    'Nombres',
                    'Estado',
                    'CodigoMVCS'
                )->first();
        if($agente){
            return response()->json($agente);
        }
        return response()->json(['error' => 'La empresa no existe'], 400);
    }

    public function getAgenteCodes(){
        $agenteList = DB::table('bdagentesservicio as a')
                        ->join('bdsolicitudes as s', 'a.CodigoMVCS', 's.CodigoMVCS')
                        ->where('s.IdTipoPersona', 3)
                        ->select('a.CodigoMVCS as code', 'a.Nombres as name')->get();
        return response()->json($agenteList);
    }
}
