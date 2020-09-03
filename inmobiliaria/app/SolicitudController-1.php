<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Agente;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\PersonaRelacionHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;

class SolicitudController extends Controller
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

    // 
    public function index()
    {
      //
    }

    public function cadenaAleatoriaLarga($longitud = 10)
    {
        
        return $cadenaAleatoria;
    }

    // CREATE
    public function crearSolicitud(Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            $ms = substr(round(microtime(true) * 1000),0,3);
            $date = date("Y-m-d H:i:s").".".$ms;
            $agente = Agente::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
            $persona = Persona::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
            $solicitud = Solicitud::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
            $usuario = Usuario::where('NUsuario',trim($data['CodigoMVCS']))->first();
            $id = Solicitud::max('IdbdSolicitudes');
            $id=str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

                if($agente && trim($data['IdTipoPersona']) == 1){
                    $nombres = explode(" ",$agente->Nombres);
                    if(count($nombres)==2){
                        $n1 = $nombres[0];
                        $n2 = "";
                        $a1 = $nombres[1];
                        $a2 = "";
                    }
                    elseif(count($nombres)==3){
                        $n1 = $nombres[0];
                        $n2 = "";
                        $a1 = $nombres[1];
                        $a2 = $nombres[2];
                    }
                    else{
                        $n1 = $nombres[0];
                        $n2 = $nombres[1];
                        $a1 = $nombres[2];
                        $a2 = $nombres[3];
                    }
                
                    if($data['CodigoMVCS'] && !trim($data['CodigoMVCSPadre'])){
                        if(count($data) == 5){ 
                            if (!$usuario){ 
                                if (!$solicitud){ 
                                    if ($agente){
                                        if (substr($agente->NumDocumento,0,2) != 20){
                                            if (!$usuario){
                                                if (!$persona){
                                                    // INGRESA SOLICITUD
                                                    $solicitud = Solicitud::create([
                                                        'IdbdSolicitudes' => $id,
                                                        'CodigoMVCS' => $data['CodigoMVCS'],
                                                        'CodigoMVCSPadre' => $data['CodigoMVCSPadre'],
                                                        'Telefono' =>  $data['Telefono'],
                                                        'Mensaje' =>  $data['Mensaje'],
                                                        'IdTipoPersona'=> $data['IdTipoPersona'],
                                                        'Estado'=>  0,
                                                        'PrimerNombre'=>  $n1,
                                                        'SegundoNombre'=>  $n2,
                                                        'ApellidoPaterno'=>  $a1,
                                                        'ApellidoMaterno'=>  $a2,
                                                        'DocumentoID'=>  $agente->NumDocumento,
                                                        'FechaCreacion'=> $date

                                                    ]);
                                                    return response()->json(['Solicitud creada correctamente.'],201);
                                                }
                                                else{
                                                    return response()->json(['Código MVCS incorrecto.'],401);
                                                }
                                            }
                                            else{
                                                return response()->json(['El usuario ya existe.'],401);
                                            }
                                        }
                                        else{
                                            return response()->json(['Número de documento incorrecto.'],401);
                                        }
                                    }
                                    else{
                                        return response()->json(['Código MVCS incorrecto.'],401);
                                    }
                                }
                                else{ 
                                    return response()->json(['El usuario ya tiene una solicitud activa.'],401);
                                }
                            }
                            else{ 
                                return response()->json(['El usuario ya esta registrado en la aplicación.'],401);
                            }
                        }   
                        else{
                            return response()->json(['El número de campos enviados es incorrecto.'],401);
                        }
                    }
                    else{
                        $personaBroker = Persona::where('Num_DocumentoID',$agente->NumDocumento)->first();
                        if($personaBroker AND $personaBroker->IdTipoPersona == 3){ 
                            return response()->json(['error' => 'Un broker no puede afiliarse a otro broker.'],401);
                        } 
                        else{
                            $existe_afiliacion = PersonaRelacionHist::where('CodigoMVCS', trim($solicitud->CodigoMVCS))->first();
                            if($existe_afiliacion && $existe_afiliacion->Flg_EstadoAfiliado != 1){
                                return response()->json(['Usuario afiliado a la empresa.'],401);
                            }   
                            else{
                                return response()->json(['El usuario ya esta afiliado a una empresa.'],401);
                            }
                        }
                    }
                }
                else if(trim($data['IdTipoPersona']) == 3){
                    if(count($data) == 5){
                        // $agente = Agente::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
                        // $persona = Persona::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
                        // $solicitud = Solicitud::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();
                        if (!$solicitud){ 
                            if ($agente){
                                if (substr($agente->NumDocumento,0,2) == 20){
                                    if (!$usuario){
                                        if (!$persona){
                                            // INGRESA SOLICITUD
                                            $solicitud = Solicitud::create([
                                                'IdbdSolicitudes' => $id,
                                                'CodigoMVCS' => $data['CodigoMVCS'],
                                                'CodigoMVCSPadre' => $data['CodigoMVCSPadre'],
                                                'Telefono' =>  $data['Telefono'],
                                                'Mensaje' =>  $data['Mensaje'],
                                                'IdTipoPersona'=> $data['IdTipoPersona'],
                                                'FechaCreacion'=> $date,
                                                'Estado'=>  0
                                            ]);
                                            $persona = Persona::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();

                                            return response()->json(['Solicitud creada correctamente.'],201);
                                        }
                                        else{
                                            return response()->json(['Código MVCS incorrecto.'],401);
                                        }
                                    }
                                    else{
                                        return response()->json(['El usuario ya existe.'],401);
                                    }
                                }
                                else{
                                    return response()->json(['Número de documento incorrecto.'],401);
                                }
                            }
                            else{
                                return response()->json(['Código MVCS incorrecto.'],401);
                            }
                        }
                        else{
                            return response()->json(['El usuario ya tiene una solicitud activa.'],401);
                        }
                    }
                    else{
                        return response()->json(['El número de campos enviados es incorrecto.'],401);
                    }
                }
                else if(trim($data['IdTipoPersona']) == 2){
                    if(count($data) == 11){
                        $solicitud = Solicitud::where('DocumentoID',trim($data['DocumentoID']))->where('Estado',0)->first();
                        $agente = Agente::where('CodigoMVCS',trim($data['CodigoMVCSPadre']))->first();
                        if ($agente){
                            if (!$solicitud){
                                if(!$data['CodigoMVCSPadre'] && $data['CodigoMVCS']){
                                
                                    Solicitud::create([
                                        'Telefono' => $data['Telefono'],
                                        'IdbdSolicitudes' => $id,
                                        'CodigoMVCS' => $data['CodigoMVCS'],
                                        'CodigoMVCSPadre' => $data['CodigoMVCSPadre'],
                                        'PrimerNombre' =>  $data['PrimerNombre'],
                                        'SegundoNombre' =>  $data['SegundoNombre'],
                                        'ApellidoPaterno' =>  $data['ApellidoPaterno'],
                                        'ApellidoMaterno' =>  $data['ApellidoMaterno'],
                                        'Correo' =>  $data['Correo'],
                                        'DocumentoID' =>  $data['DocumentoID'],
                                        'Mensaje' =>  $data['Mensaje'],
                                        'IdTipoPersona'=> $data['IdTipoPersona'],
                                        'Estado'=>  0,
                                        'FechaCreacion'=>  $date
                                    ]);
                                    return response()->json(['Solicitud creada correctamente.'],201);
                                }
                                else{
                                    $personaBroker = Persona::where('Num_DocumentoID',trim($data['DocumentoID']))->first();
                                    if($personaBroker AND $personaBroker->IdTipoPersona == 3){ 
                                        return response()->json(['error' => 'Un broker no puede afiliarse a otro broker.'],401);
                                    } 
                                    else{ 
                                        $persona = Persona::where('Num_DocumentoID', trim($data['DocumentoID']))->first();
                                        if(!$persona){
                                            $longitud = 8;
                                            $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                                            $cadenaAleatoria = '';
                                            for ($i = 0; $i < $longitud; $i++) {
                                                $cadenaAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
                                            }
                                            $cadenaAleatoria;

                                            $passMail = $cadenaAleatoria;
                                            $pass = Hash::make($cadenaAleatoria);
                                            $idp = Persona::max('IdPersonal');
                                            $idp = str_pad(intval($idp)+1,8,"0",STR_PAD_LEFT);
                                            $idu = Usuario::max('IdUsuario');
                                            $idu = str_pad(intval($idu)+1,8,"0",STR_PAD_LEFT);
                                            $us = Usuario::where('NUsuario','like','%DEP%')->max('NUsuario');
                                            if(!$us){
                                                $usr = 1;
                                            }
                                            else{
                                                $usr = intval(str_replace('DEP', '', $us)) + 1;
                                            }
                                            
                                            $usr = "DEP".str_pad($usr,5,"0",STR_PAD_LEFT);

                                            Solicitud::create([
                                                'Telefono' => $data['Telefono'],
                                                'IdbdSolicitudes' => $id,
                                                'CodigoMVCS' => $data['CodigoMVCS'],
                                                'CodigoMVCSPadre' => $data['CodigoMVCSPadre'],
                                                'PrimerNombre' =>  $data['PrimerNombre'],
                                                'SegundoNombre' =>  $data['SegundoNombre'],
                                                'ApellidoPaterno' =>  $data['ApellidoPaterno'],
                                                'ApellidoMaterno' =>  $data['ApellidoMaterno'],
                                                'Correo' =>  $data['Correo'],
                                                'DocumentoID' =>  $data['DocumentoID'],
                                                'Mensaje' =>  $data['Mensaje'],
                                                'IdTipoPersona'=> $data['IdTipoPersona'],
                                                'Estado'=>  0,
                                                'FechaCreacion'=>  $date
                                            ]);

                                            Persona::create([
                                                'IdPersonal' => $idp,
                                                'CodigoMVCS' => $agente->codigoMVCS,
                                                'Des_NombreCompleto' =>  $agente->Nombres,
                                                'Num_DocumentoID' =>  $agente->NumDocumento,
                                                'Des_Correo1' =>  $agente->Correo,
                                                'Des_Telefono1' =>  $data['Telefono'],
                                                'IdTipoPersona'=>  3,
                                                'Flg_Estado'=>  1,
                                                'FechaCreacion'=>  $date,
                                            ]);
                                        
                                            Usuario::create([
                                                'IdUsuario' => $idu,
                                                'IdPersonal' => $idp,
                                                'NUsuario' =>  $data['CodigoMVCSPadre'],
                                                'NContrasenia' =>  $pass,
                                                'Flg_Estado' =>  1,
                                                'FechaCreacion'=>  $date,
                                                'Flg_TipoUsuario'=>  3
                                                // 'UsuarioCreacion'=>  $user->NUsuario
                                            ]); 
                    
                                            $usuarioUpdate = (object) $usuarioUpdate = [ 
                                                'name' => $data['CodigoMVCSPadre'],
                                                'pass' => $passMail,
                                                'ustype' => $data['IdTipoPersona']
                                            ];
                    
                                            $for = trim($agente->Correo);
                    
                                            Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                                                $msj->from('support@wilinii.com.mx', 'Winlii');
                                                $msj->subject('Bienvenido a Wlinii');
                                                $msj->to($for);
                                            });
                                            return response()->json(['Se ha enviado un mensaje al broker para realizar la afiliación.'],201);
                                        }
                                        else{
                                            return response()->json(['El usuario ya tiene esta registrado en wlinni.'],401);
                                    }
                                    }
                                }
                            }
                            else{
                                return response()->json(['El usuario ya tiene una solicitud activa.'],401);
                            }
                        }
                        else{
                            return response()->json(['El código introducido es incorrecto.'],401);
                        }
                    }
                    else{
                        return response()->json(['El número de campos enviados es incorrecto.'],401);
                    }
                }
                else{
                    return response()->json(['El tipo de persona enviado es incorrecto.'],401);
                }
        }
        return response()->json(['error' => 'No autorizado'],401);
    }

    // public function crearSolicitudDep(Request $request)
    // {
    //     if($request->isJson()){
    //         $data = $request->json()->all();
           
    //         return response()->json($solicitud, 201);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }

    //READ ALL
    public function mostrarSolicitudes(Request $request)
    {
        if($request->isJson()){
            $solicitud = Solicitud::All();
            return response()->json($solicitud,200);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }


    // //UPDATE BROKER
    // public function autorizarBrokerSolicitud($id, Request $request)
    // {
    //     if($request->isJson()){
    //         $solicitud = Solicitud::findOrFail($id);
    //         $solicitud = Solicitud::where('CodigoMVCSPadre',trim($data['CodigoMVCS']));
    //         return response()->json($solicitud);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }

    // public function update($id, $act)
    // {
    //     $id = str_pad($id,8,"0",STR_PAD_LEFT); 
    //     $solicitudUpdate = Solicitud::findOrFail($id);
    //     $for = trim($solicitudUpdate->Correo);       

    //     $solicitudUpdate->Estado = $act;
    //     $solicitudUpdate->save();

    //     $solicitudUpdate = Solicitud::findOrFail($id);

    //     if($solicitudUpdate->Estado == 1){
    //         $longitud = 8;
    //         $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!"#$%&/()?¡+{}-,;.:[]';
    //         $cadenaAleatoria = '';
    //         for ($i = 0; $i < $longitud; $i++) {
    //             $cadenaAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
    //         }
    //         $cadenaAleatoria;

    //         $passMail = $cadenaAleatoria;
    //         $pass = Hash::make($cadenaAleatoria);
    //         $user =  Usuario::where('NUsuario', 'Administrador')->first();
    //         $idp = Persona::max('IdPersonal');
    //         $idp = str_pad(intval($idp)+1,8,"0",STR_PAD_LEFT);

    //         $id = Usuario::max('IdUsuario');
    //         $id = str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);
    //         $us = Usuario::where('NUsuario','like','%DEP%')->first();

    //         $idh = PersonaRelacion_Hist::max('IdPersonaRelacion');
    //         $idh = str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);

    //         if(!$us){
    //             $usr = 1;
    //         }
    //         else{
    //             $usr = intval(str_replace('DEP', '', $us->NUsuario))+1;
    //         }
           
    //         $usr = "DEP".str_pad($usr,5,"0",STR_PAD_LEFT);

    //         $ms = substr(round(microtime(true) * 1000),0,3);
    //             $date = date("Y-m-d H:i:s").".".$ms;

    //         if($solicitudUpdate->IdTipoPersona == 1 || $solicitudUpdate->IdTipoPersona == 3){
    //             $agente = AgenteModel::where('CodigoMVCS', $solicitudUpdate->CodigoMVCS)->first();

    //             Persona::create([
    //                 'IdPersonal' => $idp,
    //                 'CodigoMVCS' => $agente->codigoMVCS,
    //                 'Des_NombreCompleto' =>  $agente->Nombres,
    //                 'Num_DocumentoID' =>  $agente->NumDocumento,
    //                 'Des_Correo1' =>  $agente->Correo,
    //                 'Des_Telefono1' =>  $solicitudUpdate->Telefono,
    //                 'IdTipoPersona'=>  $solicitudUpdate->IdTipoPersona,
    //                 'Flg_Estado'=>  1,
    //                 'FechaCreacion'=>  $date,
    //                 'UsuarioCreacion'=>  $user->NUsuario
    //             ]);
                
    //             Usuario::create([
    //                 'IdUsuario' => $id,
    //                 'IdPersonal' => $idp,
    //                 'NUsuario' =>  $agente->CodigoMVCS,
    //                 'NContrasenia' =>  $pass,
    //                 'Flg_Estado' =>  1,
    //                 'FechaCreacion'=>  $date,
    //                 'UsuarioCreacion'=>  $user->NUsuario,
    //                 'Flg_TipoUsuario'=>  $solicitudUpdate->IdTipoPersona
    //             ]);

    //             // if ($solicitudUpdate->IdTipoPersona == 3){
    //             //     PersonaRelacionHist::create([
    //             //         'IdPersonaRelacion' => $idh,
    //             //         'CodigoMVCSPadre' => $agente->CodigoMVCS,
    //             //         'CodigoMVCS' =>  $agente->CodigoMVCS,
    //             //         'Fec_Inicio' =>  $date,
    //             //         'Fec_Fin' =>  $date,
    //             //         'Flg_EstadoAfiliado' =>  1,
    //             //         'FechaCreacion'=>  $date,
    //             //         'UsuarioCreacion'=>  $user->NUsuario,
    //             //         'FechaModificacion'=>  '',
    //             //         'UsuarioModificacion'=>  '',
    //             //     ]);
    //             }

    //             $usuarioUpdate = (object) $usuarioUpdate = [ 
    //                 'name' => $agente->CodigoMVCS,
    //                 'pass' => $passMail,
    //                 'ustype' => $solicitudUpdate->IdTipoPersona
    //             ];
            
    //             Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
    //                 $msj->from('support@wilinii.com.mx', 'Winlii');
    //                 $msj->subject('Bienvenido a Wlinii');
    //                 $msj->to($for);
    //             });

    //         }
    //         else if($solicitudUpdate->IdTipoPersona == 2){
               
    //             Persona::create([
    //                 'IdPersonal' => $idp,
    //                 'CodigoMVCS' => $solicitudUpdate->codigoMVCSPadre,
    //                 'PrimerNombre' =>  $solicitudUpdate->PrimerNombre,
    //                 'SegundoNombre' =>  $solicitudUpdate->SegundoNombre,
    //                 'ApellidoPaterno' =>  $solicitudUpdate->ApellidoPaterno,
    //                 'ApellidoMaterno' =>  $solicitudUpdate->ApellidoMaterno,
    //                 'Des_Correo1' =>  $solicitudUpdate->Correo,
    //                 'Num_DocumentoID' =>  $solicitudUpdate->DocumentoID,
    //                 'IdTipoPersona'=>  $solicitudUpdate->IdTipoPersona,
    //                 'Flg_Estado'=>  1,
    //                 'FechaCreacion'=>  $date,
    //                 'UsuarioCreacion'=>  $user->NUsuario
    //             ]);
                
    //             Usuario::create([
    //                 'IdUsuario' => $id,
    //                 'IdPersonal' => $idp,
    //                 'NUsuario' =>  $usr,
    //                 'NContrasenia' =>  $pass,
    //                 'Flg_Estado' =>  1,
    //                 'FechaCreacion'=>  $date,
    //                 'UsuarioCreacion'=>  $user->Nusuario,
    //                 'Flg_TipoUsuario'=>  $solicitudUpdate->IdTipoPersona
    //             ]);

    //             $usuarioUpdate = (object) $usuarioUpdate = [ 
    //                 'name' => $usr,
    //                 'pass' => $passMail,
    //                 'ustype' => $solicitudUpdate->IdTipoPersona
    //             ];
            
    //             Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
    //                 $msj->from('support@wilinii.com.mx', 'Winlii');
    //                 $msj->subject('Bienvenido a Wlinii');
    //                 $msj->to($for);
    //             });

    //         }
    //         return redirect()->route('dashboard')->with('status', 'Se autorizo y se envio correo del usuario');
    //     }
    //     return redirect()->route('dashboard')->with('message', 'La solicitud ha sido rechazada');
    // }

    //READ ONE
    public function mostrarBrokerSolicitudes($id, Request $request)
    {
        if($request->isJson()){
            $broker = Persona::where('CodigoMVCS', $id)->get();
            $solicitudes = Solicitud::where('Estado', 0)->where('CodigoMVCSPadre', $id)->get();
            return response()->json([$broker, $solicitudes]);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }

    //READ ONE
    public function mostrarAgente($id, Request $request)
    {
        if($request->isJson()){
            $broker = Persona::where('CodigoMVCS', $id)->get();
            $solicitudes = Solicitud::where('Estado', 0)->where('CodigoMVCSPadre', $id)->get();
            return response()->json([$broker, $solicitudes]);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }


    //UPDATE
    public function actualizarUsuario($id, Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            $usuario = Usuario::findOrFail($id);
            $usuario->update($data);
            return response()->json($usuario, 201);
        } 
    }
    //DELETE
    // public function borrarUsuario($id, Request $request)
    // {
    //     if($request->isJson()){
    //         $usuario = Usuario::findOrFail($id);
    //         $usuario->delete();
    //         return response()->json(['msj' => 'Usuario borrado correctamente']);
    //     }
    //     return response()->json(['error' => 'No autorizado'],401);
    // }
}
