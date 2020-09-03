<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\PersonaRelacionHist;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Solicitud;
use App\Models\Agente;
use Illuminate\Support\Facades\Auth;

class AfiliaController extends Controller
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

    // LOGIN
    public function afiliarPersona($id,$staf)
    {       
        // if($request->isJson()){
            if($staf==1){ 
                $id=str_pad(intval($id),8,"0",STR_PAD_LEFT);
                $solicitud = Solicitud::where('IdbdSolicitudes',$id)->first();
                if($solicitud){
                    Solicitud::where('IdbdSolicitudes',$id)->update(['Estado' => 1]);
                    $agente = Agente::where('CodigoMVCS',trim($solicitud->CodigoMVCS))->first();
                    if ($solicitud->IdTipoPersona == 2){ 
                        $afiliado = PersonaRelacionHist::where('CodigoMVCS',trim($solicitud->CodigoMVCS))->where('Flg_EstadoAfiliado',1)->first();
                        if(!$afiliado){ 
                            $longitud = 8;
                            $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                            $cadenaAleatoria = '';
                            for ($i = 0; $i < $longitud; $i++) {
                                $cadenaAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
                            }
                            $cadenaAleatoria;
                
                            $passMail = $cadenaAleatoria;
                            $pass = Hash::make($cadenaAleatoria);
                            $user =  Usuario::where('NUsuario', 'Administrador')->first();
                            $idp = Persona::max('IdPersonal');
                            $idp = str_pad(intval($idp)+1,8,"0",STR_PAD_LEFT);
                
                            $id = Usuario::max('IdUsuario');
                            $id = str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);
                            $us = Usuario::where('NUsuario','like','%DEP%')->max('NUsuario');
                            
                            if(!$us){
                                $usr = 1;
                            }
                            else{
                                $usr = intval(str_replace('DEP', '', $us)) + 1;
                            }
                            
                            $usr = "DEP".str_pad($usr,5,"0",STR_PAD_LEFT);
                
                            $ms = substr(round(microtime(true) * 1000),0,3);
                            $date = date("Y-m-d H:i:s").".".$ms;

                            $nombreCompleto = trim($solicitud->PrimerNombre).' ';
                            if($solicitud->SegundoNombre)
                                $nombreCompleto .= trim($solicitud->SegundoNombre).' ';
                            $nombreCompleto .= trim($solicitud->ApellidoPaterno).' '. trim($solicitud->ApellidoMaterno);

                            Persona::create([
                                'IdPersonal' => $idp,
                                'Des_NombreCompleto' => $nombreCompleto,
                                'codigoMVCS' => $solicitud->CodigoMVCS,
                                'codigoMVCSPadre' => $solicitud->CodigoMVCSPadre,
                                'Des_PrimerNombre' =>  $solicitud->PrimerNombre,
                                'Des_SegundoNombre' =>  $solicitud->SegundoNombre,
                                'Des_ApePaterno' =>  $solicitud->ApellidoPaterno,
                                'Des_AperMaterno' =>  $solicitud->ApellidoMaterno,
                                'Des_Correo1' =>  $solicitud->Correo,
                                'Num_DocumentoID' =>  $solicitud->DocumentoID,
                                'IdTipoPersona'=>  $solicitud->IdTipoPersona,
                                'Flg_Estado'=>  1,
                                'FechaCreacion'=>  $date,
                                // 'UsuarioCreacion'=>  $user->NUsuario
                            ]);

                            Usuario::create([
                                'IdUsuario' => $id,
                                'IdPersonal' => $idp,
                                'NUsuario' =>  $usr,
                                'NContrasenia' =>  $pass,
                                'Flg_Estado' =>  1,
                                'FechaCreacion'=>  $date,
                                'UsuarioCreacion'=>  $user->Nusuario,
                                'Flg_TipoUsuario'=>  $solicitud->IdTipoPersona,
                                // 'UsuarioCreacion'=>  $user->NUsuario
                            ]);

                            $persona = Persona::where('Num_DocumentoID', trim($solicitud->DocumentoID))->first();
                            $idper = $persona->IdPersonal;
                            $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                            $personaBroker = Agente::where('CodigoMVCS', trim($solicitud->CodigoMVCSPadre))->first();
                            $personaBroker = Persona::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                            $idperpad = $personaBroker->IdPersonal;
                            $idperpad = str_pad(intval($idperpad),8,"0",STR_PAD_LEFT);
                            $idprh = PersonaRelacionHist::max('IdPersonaRelacion');
                            $idprh = str_pad(intval($idprh)+1,8,"0",STR_PAD_LEFT);
                            
                            $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                            PersonaRelacionHist::create([
                                'IdPersonaRelacion' => $idprh,
                                'IdPersonal' => $idper,
                                'IdPersonalPadre' => $idperpad,
                                'CodigoMVCSPadre' => $solicitud->CodigoMVCSPadre,
                                'CodigoMVCS' =>  $solicitud->CodigoMVCS,
                                'Fec_Inicio' =>  $date,
                                'Fec_Fin' =>  $datefin,
                                'Flg_EstadoAfiliado' =>  1,
                                'FechaCreacion'=>  $date,
                                // 'UsuarioCreacion'=>  $user->NUsuario
                            ]);

                            $usuarioUpdate = (object) $usuarioUpdate = [ 
                                'name' => $usr,
                                'pass' => $passMail,
                                'ustype' => $solicitud->IdTipoPersona,
                                'fullname' => $nombreCompleto
                            ];

                            $for = trim($solicitud->Correo); 

                            // Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                            //     $msj->from('support@wilinii.com.mx', 'Winlii');
                            //     $msj->subject('Bienvenido a Wlinii');
                            //     $msj->to($for);
                            // });
                            return response()->json(['message'=>'Persona afiliada correctamente.'], 201);
                        }
                        else{
                            return response()->json(['error'=>'Persona ya afiliada a una empresa.'], 400);
                        }
                    }
                    else if ($solicitud->IdTipoPersona == 1){ 
                        $afiliado = PersonaRelacionHistModel::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_EstadoAfiliado', 1)->first();
                        if(!$afiliado){
                            $persona = Persona::where('Num_DocumentoID', trim($solicitud->DocumentoID))->first();
                            if($persona){ 
                                $idper = $persona->IdPersonal;
                                $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                                $personaBroker = Agente::where('CodigoMVCS', trim($solicitud->CodigoMVCSPadre))->first();
                                $personaBroker = Persona::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                                $idperpad = $personaBroker->IdPersonal;
                                $idperpad = str_pad(intval($idperpad),8,"0",STR_PAD_LEFT);
                                $idprh = PersonaRelacionHist::max('IdPersonaRelacion');
                                $idprh = str_pad(intval($idprh)+1,8,"0",STR_PAD_LEFT);
                                
                                $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                                PersonaRelacionHist::create([
                                    'IdPersonaRelacion' => $idprh,
                                    'IdPersonal' => $idper,
                                    'IdPersonalPadre' => $idperpad,
                                    'CodigoMVCSPadre' => $solicitud->CodigoMVCSPadre,
                                    'CodigoMVCS' =>  $solicitud->CodigoMVCS,
                                    'Fec_Inicio' =>  $date,
                                    'Fec_Fin' =>  $datefin,
                                    'Flg_EstadoAfiliado' =>  1,
                                    'FechaCreacion'=>  $date,
                                    // 'UsuarioCreacion'=>  $user->NUsuario
                                ]);
                                return response()->json(['message'=>'Persona afiliada correctamente.'], 201);
                            }
                            else{
                                $idp = Persona::max('IdPersonal');
                                $idp = str_pad(intval($idp)+1,8,"0",STR_PAD_LEFT);
                                $agente = Agente::where('CodigoMVCS',trim($solicitud->CodigoMVCS))->first();

                                PersonaModel::create([
                                    'IdPersonal' => $idp,
                                    'CodigoMVCS' => $agente->codigoMVCS,
                                    'Des_NombreCompleto' =>  $agente->Nombres,
                                    'Num_DocumentoID' =>  $agente->NumDocumento,
                                    'Des_Correo1' =>  $agente->Correo,
                                    'Des_Telefono1' =>  $solicitudUpdate->Telefono,
                                    'IdTipoPersona'=>  $solicitudUpdate->IdTipoPersona,
                                    'Flg_Estado'=>  1,
                                    'FechaCreacion'=>  $date,
                                    'UsuarioCreacion'=>  $user->NUsuario
                                ]);
                                
                                UsuarioModel::create([
                                    'IdUsuario' => $id,
                                    'IdPersonal' => $idp,
                                    'NUsuario' =>  $agente->CodigoMVCS,
                                    'NContrasenia' =>  $pass,
                                    'Flg_Estado' =>  1,
                                    'FechaCreacion'=>  $date,
                                    'UsuarioCreacion'=>  $user->NUsuario,
                                    'Flg_TipoUsuario'=>  $solicitudUpdate->IdTipoPersona
                                ]);
        
                                $usuarioUpdate = (object) $usuarioUpdate = [ 
                                    'name' => $agente->CodigoMVCS,
                                    'pass' => $passMail,
                                    'ustype' => $solicitudUpdate->IdTipoPersona
                                ];
            
                                $for = trim($agente->Correo);
                                
                                Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                                     $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                                    $msj->subject('Bienvenido a Wlinii');
                                    $msj->to($for);
                                });
                            }
                        }
                        else{ 
                            return redirect()->back()->with('status', 'Persona ya registrada a una empresa.');
                        }
                    }
                    else{
                        return response()->json(['error'=>'Tipo de persona incorrecto.'], 401);
                    }
                }
                else{
                    return response()->json(['error'=>'No hay una solicitud activa.'], 401);
                }
            }
            else{
                $id=str_pad(intval($id),8,"0",STR_PAD_LEFT);
                $usuario = Usuario::where('IdPersonal',$id)->first();
                $persona = Persona::where('IdPersonal',$id)->first();
                $afiliado = PersonaRelacionHist::where('IdPersonal',$id)->where('Flg_EstadoAfiliado',1)->first();
                if($afiliado){ 
                    if($persona->IdTipoPersona == 2){ 
                        $data = [
                            'Flg_Estado' => 0
                        ];
                        $usuario->update($data);
                    }
                    $data = [
                        'Flg_EstadoAfiliado' => 0
                    ];
                    $afiliado->update($data);
                    return response()->json(['message'=>'Persona desafiliada correctamente.'], 201);
                }
                else{
                    return response()->json(['error'=>'Persona no afiliada a una empresa.'], 400);
                }
            }            
        // } 
    }   
}
