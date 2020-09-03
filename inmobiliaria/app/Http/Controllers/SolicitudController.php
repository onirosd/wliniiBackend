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

                if($data['IdTipoPersona'] == 1){                
                    if($data['CodigoMVCS'] && !trim($data['CodigoMVCSPadre'])){
                        if(count($data) == 5){ 
                            if (!$usuario){ 
                                if (!$solicitud){ 
                                    if ($agente){
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

                                                    $mensaje = "<p>¡ATENCIÓN!</p> Verifique su código MVCS o comuníquese<br>";
                                                    $mensaje .= "con nosotros en caso de haberse registrado recientemente en el MVCS.";
                                                    return response()->json(['error' => $mensaje],401);
                                                }
                                            }
                                            else{
                                                return response()->json(['error' => 'El usuario ya existe.'],401);
                                            }
                                        }
                                        else{
                                            return response()->json(['error' => 'Número de documento incorrecto.'],401);
                                        }
                                    }
                                    else{
                                        
                                         $mensaje = "<p>¡ATENCIÓN!</p> Verifique su código MVCS o comuníquese<br>";
                                         $mensaje .= "con nosotros en caso de haberse registrado recientemente en el MVCS.";
                                         return response()->json(['error' => $mensaje],401);
                                    }
                                }
                                else{ 
                                    return response()->json(['error' => 'El usuario ya tiene una solicitud activa.'],401);
                                }
                            }
                            else{ 
                                return response()->json(['error' => 'El usuario ya esta registrado en la aplicación.'],401);
                            }
                        }   
                        else{
                            return response()->json(['error' => 'El número de campos enviados es incorrecto.'],401);
                        }
                    }
                    else{
                        $personaBroker = Agente::where('CodigoMVCS', $data['CodigoMVCSPadre'])->first();
                        if($personaBroker){ 
                            if ($agente){
                                $personaBroker = Persona::where('Num_DocumentoID',trim($agente->NumDocumento))->first();
                                if($personaBroker AND $personaBroker->IdTipoPersona == 3){ 
                                    return response()->json(['error' => 'Un broker no puede afiliarse a otro broker.'],401);
                                } 
                                else{   
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

                                    $personaBroker = Agente::where('CodigoMVCS', $data['CodigoMVCSPadre'])->first();
                                    $existeBroker = Persona::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();

                                    if(!$existeBroker){ 
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
                                        
                                        Persona::create([
                                            'IdPersonal' => $idp,
                                            'CodigoMVCS' => $personaBroker->codigoMVCS,
                                            'Des_NombreCompleto' =>  $personaBroker->Nombres,
                                            'Num_DocumentoID' =>  $personaBroker->NumDocumento,
                                            'Des_Correo1' =>  $personaBroker->Correo,
                                            'Des_Telefono1' =>  $data['Telefono'],
                                            'IdTipoPersona'=>  1,
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
                                            'Flg_TipoUsuario'=>  1
                                            // 'UsuarioCreacion'=>  $user->NUsuario
                                        ]); 
            
                                        $usuarioUpdate = (object) $usuarioUpdate = [ 
                                            'name' => $data['CodigoMVCSPadre'],
                                            'pass' => $passMail,
                                            'ustype' => 1
                                        ];
                
                                        $for = trim($personaBroker->Correo);

                                       
                                        Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                                            $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                                            $msj->subject('Bienvenido a Wlinii');
                                            $msj->to($for);
                                        });
                                        return response()->json(['Se ha enviado un mensaje al broker para realizar la afiliación.'],201);
                                    }
                                    else{
                                        /* Tipo 1 */
                                        $persona = Agente::where('CodigoMVCS', $data['CodigoMVCS'])->first();
                                        $persona = Persona::where('Num_DocumentoID', trim($persona->NumDocumento))->first();
                                        if($persona){
                                            $afiliado = PersonaRelacionHist::where('CodigoMVCS', $data['CodigoMVCS'])->where('Flg_EstadoAfiliado', 1)->first();
                                            if(!$afiliado){ 
                                                $solicitud = Solicitud::where('CodigoMVCS',$data['CodigoMVCS'])->where('Estado',0)->first();
                                                if(!$solicitud){
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
                                                    return response()->json(['error' => 'La solicitud ya ha sido creada.'],401);
                                                }
                                            }
                                            else{
                                                return response()->json(['error' => 'El usuario ya esta afiliado a una empresa.'],401);
                                            }  
                                        }     
                                        else{
                                            if(!$solicitud){ 
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
                                                return response()->json(['Solicitud creada correctamente'],201);
                                            }
                                            else{
                                                return response()->json(['error' => 'La solicitud ya ha sido creada.'],401);
                                            }
                                        } 
                                    }                                                            
                                }
                            }
                            else{
                                return response()->json(['error' => 'El código introducido es incorrecto.'],401);
                            }
                        }
                        else{
                            return response()->json(['error' => 'El broker no esta registrado en el ministerio.'],401);
                        }
                    }
                }
                else if(trim($data['IdTipoPersona']) == 3){
                    if($data['CodigoMVCS'] && !trim($data['CodigoMVCSPadre'])){
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
                                                return response()->json(['error' => 'Código MVCS incorrecto.'],401);
                                            }
                                        }
                                        else{
                                            return response()->json(['error' => 'El usuario ya existe.'],401);
                                        }
                                    }
                                    else{
                                        return response()->json(['error' => 'Número de documento incorrecto.'],401);
                                    }
                                }
                                else{
                                    return response()->json(['error' => 'Código MVCS incorrecto.'],401);
                                }
                            }
                            else{
                                return response()->json(['error' => 'El usuario ya tiene una solicitud activa.'],401);
                            }
                        }
                        else{
                            return response()->json(['error' => 'El número de campos enviados es incorrecto.'],401);
                        }
                    }
                    else{
                        $agenteBroker = Agente::where('CodigoMVCS',trim($data['CodigoMVCSPadre']))->first();
                        if($agenteBroker){ 
                            return response()->json(['error' => 'Un broker no puede afiliarse a otro broker.'],401);
                        } 
                    }
                }
                else if(trim($data['IdTipoPersona']) == 2){
                    if(count($data) == 11){
                        $solicitud = Solicitud::where('DocumentoID',trim($data['DocumentoID']))->where('Estado',0)->first();
                        $agente    = Agente::where('CodigoMVCS',trim($data['CodigoMVCSPadre']))->first();

                       // echo $solicitud;
                        if ($agente){
                            $agenteExt = Agente::where('CodigoMVCS',trim($data['CodigoMVCS']))->first();                        
                            if(!$agenteExt){
                                if (!$solicitud){
                                    if($data['CodigoMVCSPadre'] && !$data['CodigoMVCS']){
                                    
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
                                        return response()->json(['Solicitud creada correctamente.']);
                                    }
                                   /*else if($data['CodigoMVCSPadre'] && $data['CodigoMVCS']){
                                        $persona = Persona::where('Num_DocumentoID', trim($data['DocumentoID']))->first();
                                        $usuario = Usuario::where('IdPersonal',trim($persona->IdPersonal))->first();
                                        $usuarioHab = Usuario::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_Estado', 1)->first();
                                        $afiliado = PersonaRelacionHist::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_EstadoAfiliado', 1)->first();
                                        if($usuario != $data['CodigoMVCS'] && !$usuarioHab && !$afiliado){
                                            return response()->json(['El usuario ya esta dado de alta, requiere usar su código registrado.'],201);
                                        } 
                                    }*/
                                    else{

                                        $persona = Persona::where('Num_DocumentoID', trim($data['DocumentoID']))->first();  
                                        $usuario = Usuario::where('IdPersonal',trim($persona->IdPersonal))->first();
                                        $usuarioHab = Usuario::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_Estado', 1)->first();
                                        $afiliado = PersonaRelacionHist::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_EstadoAfiliado', 1)->first();
                                        if($usuario->NUsuario != $data['CodigoMVCS'] && !$usuarioHab && !$afiliado){
                                            return response()->json(['El usuario esta inhabilitado o afiliado y no puede crear la solicitud.'],201);
                                        } 
                                        else if($data['CodigoMVCSPadre']  && $usuario->NUsuario == $data['CodigoMVCS'] && !$usuarioHab->NUsuario && !$afiliado->IdPersonal){
                                            $solicitud = Solicitud::where('DocumentoID',$data['DocumentoID'])->where('Estado',0)->first();
                                            if (!$solicitud){
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
                                                return response()->json(['error' => 'El usuario ya tiene una solicitud activa.'],401);
                                            }
                                        }
                                        else{
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

                                                $personaBroker = Agente::where('CodigoMVCS', $data['CodigoMVCSPadre'])->first();
                                                $existeBroker = Persona::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();

                                                if(!$existeBroker){ 
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
                                                         $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                                                        $msj->subject('Bienvenido a Wlinii');
                                                        $msj->to($for);
                                                    });
                                                    return response()->json(['Se ha enviado un mensaje al broker para realizar la afiliación.'],201);
                                                }
                                                return response()->json(['Solicitud creada correctamente.'],201);
                                            }
                                            else{
                                                $usuario =  Usuario::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_Estado', 0)->first();
                                                $afiliado = PersonaRelacionHist::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_EstadoAfiliado', 0)->first();
                                                if($usuario AND $afiliado){
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

                                                    $personaBroker = Agente::where('CodigoMVCS', $data['CodigoMVCSPadre'])->first();
                                                    $existeBroker = Persona::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();

                                                    if(!$existeBroker){ 
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
                                                            $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                                                            $msj->subject('Bienvenido a Wlinii');
                                                            $msj->to($for);
                                                        });
                                                        return response()->json(['Se ha enviado un mensaje al broker para realizar la afiliación.'],201);
                                                    }
                                                    return response()->json(['Solicitud creada correctamente.'],201);
                                                }
                                            }
                                        }
                                    }
                                }
                                else{
                                    return response()->json(['error' => 'El usuario ya tiene una solicitud activa.'],401);
                                }
                            }
                            else{
                                return response()->json(['error' => 'El usuario no puede generar una solicitud.'],401);
                            }
                        }
                        else{
                            return response()->json(['error' => 'El código introducido es incorrecto.'],401);
                        }
                    }
                    else{
                        return response()->json(['error' => 'El número de campos enviados es incorrecto.'],401);
                    }
                }
                else{
                    return response()->json(['error' => 'El tipo de persona enviado es incorrecto.'],401);
                }
        }
        return response()->json(['error' => 'No autorizado'],401);
    }

    //READ ALL
    public function mostrarSolicitudes(Request $request)
    {
        if($request->isJson()){
            $solicitud = Solicitud::All();
            return response()->json($solicitud,200);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }

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
