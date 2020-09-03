<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\UsuarioModel;
use App\SolicitudModel;
use App\AgenteModel;
use App\PersonaModel;
use App\PersonaRelacionHistModel;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function login(Request $request)
    {       
        
        $user =  UsuarioModel::where('NUsuario', 'Administrador')->first();
        $solicitud = SolicitudModel::where('Estado', 0)->get();

        //echo $password = Hash::make('admin');
      
        $password = $request->input('password');
        $usuario = $request->input('user');
        
        if($user){ 
            
            if (Hash::check($password, trim($user->NContrasenia))){
                return redirect()->route('dashboard', ['solicitudes' => $solicitud]);
                // return redirect()->route('admin', ['user' => $user]);
            }
            else{
                return redirect()->back()->with('error', 'Datos Invalidos');
            }
        }
        else{
            return view('login');
        }
    }
    public function dashboard()
    {       
        $solicitud = SolicitudModel::where('Estado', 0)->get();
        $agente = AgenteModel::all();

        return view('dashboard', ['solicitudes' => $solicitud, 'agentes' => $agente]);
    }

    public function update($id, $act)
    {
        $id = str_pad($id,8,"0",STR_PAD_LEFT); 
        $solicitudUpdate = SolicitudModel::findOrFail($id);
        $for = trim($solicitudUpdate->Correo);       

        $solicitudUpdate->Estado = $act;
        $solicitudUpdate->save();

        $solicitudUpdate = SolicitudModel::findOrFail($id);

        if($solicitudUpdate->Estado == 1){
            $longitud = 5;
            $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $cadenaAleatoria = '';
            for ($i = 0; $i < $longitud; $i++) {
                $cadenaAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
            }
            $cadenaAleatoria;

            $passMail = $cadenaAleatoria;
            $pass = Hash::make($cadenaAleatoria);
            $user =  UsuarioModel::where('NUsuario', 'Administrador')->first();
            $idp = PersonaModel::max('IdPersonal');
            $idp = str_pad(intval($idp)+1,8,"0",STR_PAD_LEFT);

            $id = UsuarioModel::max('IdUsuario');
            $id = str_pad(intval($id)+1,8,"0",STR_PAD_LEFT);
            $us = UsuarioModel::where('NUsuario','like','%DEP%')->max('NUsuario');
            
            if(!$us){
                $usr = 1;
            }
            else{
                $usr = intval(str_replace('DEP', '', $us)) + 1;
            }
            
            $usr = "DEP".str_pad($usr,5,"0",STR_PAD_LEFT);

            $ms = substr(round(microtime(true) * 1000),0,3);
            $date = date("Y-m-d H:i:s").".".$ms;

            if($solicitudUpdate->IdTipoPersona == 1){
                if(!trim($solicitudUpdate->CodigoMVCSPadre) && $solicitudUpdate->CodigoMVCS){
                    $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCS))->first();
                    $persona = PersonaModel::where('Num_DocumentoID', $solicitudUpdate->DocumentoID)->orWhere('Num_DocumentoID', $agente->NumDocumento)->first();
                    if(!$persona){ 
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

                        // if ($solicitudUpdate->CodigoMVCSPadre){ 
                        //     $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                        //     PersonaRelacionHistModel::create([
                        //         'IdPersonaRelacion' => $idprh,
                        //         'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                        //         'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                        //         'Fec_Inicio' =>  $date,
                        //         'Fec_Fin' =>  $datefin,
                        //         'Flg_EstadoAfiliado' =>  1,
                        //         'FechaCreacion'=>  $date,
                        //         'UsuarioCreacion'=>  $user->NUsuario
                        //     ]);
                        // }

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
                    else{
                        
                        return redirect()->back()->with('status', 'El usuario ya existe en wlinii.');
                    }
                    // $usuarioUpdate = (object) $usuarioUpdate = [ 
                    //     'name' => $usr,
                    //     'pass' => $passMail,
                    //     'ustype' => $solicitudUpdate->IdTipoPersona
                    // ];
                

                    // Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                    //     $msj->from('support@wilinii.com.mx', 'Winlii');
                    //     $msj->subject('Bienvenido a Wlinii');
                    //     $msj->to($for);
                    // });
                }
                else{
                    $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                    $usuario = UsuarioModel::where('NUsuario', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                    
                    if($agente && !$usuario){
                        
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
                    else{
                        $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCS))->first();
                        $persona = PersonaModel::where('Num_DocumentoID', $solicitudUpdate->DocumentoID)->orWhere('Num_DocumentoID', $agente->NumDocumento)->first();
                        if(!$persona){ 
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

                            $idprh = PersonaRelacionHistModel::max('IdPersonaRelacion');
                            $idprh = str_pad(intval($idprh)+1,8,"0",STR_PAD_LEFT);
                            $persona = PersonaModel::where('Num_DocumentoID', trim($solicitudUpdate->DocumentoID))->first();
                            $idper = $persona->IdPersonal;
                            $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                            $personaBroker = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                            $personaBroker = PersonaModel::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                            $idperpad = $personaBroker->IdPersonal;
                            $idperpad = str_pad(intval($idperpad),8,"0",STR_PAD_LEFT);
                            $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                            PersonaRelacionHistModel::create([
                                'IdPersonaRelacion' => $idprh,
                                'IdPersonal' => $idper,
                                'IdPersonalPadre' => $idperpad,
                                'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                                'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                                'Fec_Inicio' =>  $date,
                                'Fec_Fin' =>  $datefin,
                                'Flg_EstadoAfiliado' =>  1,
                                'FechaCreacion'=>  $date,
                                'UsuarioCreacion'=>  $user->NUsuario
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
                            return redirect()->back()->with('status', 'Usuario creado y correo enviado.');
                        }   
                        else{
                            $idprh = PersonaRelacionHistModel::max('IdPersonaRelacion');
                            $idprh = str_pad(intval($idprh)+1,8,"0",STR_PAD_LEFT);
                            $persona = PersonaModel::where('Num_DocumentoID', trim($solicitudUpdate->DocumentoID))->first();
                            $idper = $persona->IdPersonal;
                            $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                            $personaBroker = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                            $personaBroker = PersonaModel::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                            $idperpad = $personaBroker->IdPersonal;
                            $idperpad = str_pad(intval($idperpad),8,"0",STR_PAD_LEFT);
                            $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                            PersonaRelacionHistModel::create([
                                'IdPersonaRelacion' => $idprh,
                                'IdPersonal' => $idper,
                                'IdPersonalPadre' => $idperpad,
                                'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                                'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                                'Fec_Inicio' =>  $date,
                                'Fec_Fin' =>  $datefin,
                                'Flg_EstadoAfiliado' =>  1,
                                'FechaCreacion'=>  $date,
                                'UsuarioCreacion'=>  $user->NUsuario
                            ]);
                            return redirect()->back()->with('status', 'Usuario afiliado correctamente.');
                        }
                    }
                }
            }
            else if($solicitudUpdate->IdTipoPersona == 3){
                $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCS))->first();
                $persona = PersonaModel::where('Num_DocumentoID', $solicitudUpdate->DocumentoID)->orWhere('Num_DocumentoID', $agente->NumDocumento)->first();
                if(!$persona){
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
                else{
                    return redirect()->back()->with('status', 'El usuario ya existe en wlinii.');
                }

                // $usuarioUpdate = (object) $usuarioUpdate = [ 
                //     'name' => $usr,
                //     'pass' => $passMail,
                //     'ustype' => $solicitudUpdate->IdTipoPersona
                // ];
 
            
                // Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                //     $msj->from('support@wilinii.com.mx', 'Winlii');
                //     $msj->subject('Bienvenido a Wlinii');
                //     $msj->to($for);
                // });

            }
            else if($solicitudUpdate->IdTipoPersona == 2){
                $idprh = PersonaRelacionHistModel::max('IdPersonaRelacion');
                $idprh = str_pad(intval($idprh)+1,8,"0",STR_PAD_LEFT);
                $solicitudUpdate->CodigoMVCS = trim($solicitudUpdate->CodigoMVCS); 
                $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();

                if($solicitudUpdate->CodigoMVCSPadre && !$solicitudUpdate->CodigoMVCS){
                    $persona = PersonaModel::where('Num_DocumentoID', $solicitudUpdate->DocumentoID)->first();
                    if(!$persona){
                        $nombreCompleto = trim($solicitudUpdate['PrimerNombre']).' ';
                        if($solicitudUpdate['SegundoNombre'])
                            $nombreCompleto .= trim($solicitudUpdate['SegundoNombre']).' ';
                        $nombreCompleto .= trim($solicitudUpdate['ApellidoPaterno']).' '. trim($solicitudUpdate['ApellidoMaterno']);
                        PersonaModel::create([
                            'IdPersonal' => $idp,
                            'Des_NombreCompleto' => $nombreCompleto,
                            'codigoMVCS' => $solicitudUpdate->CodigoMVCS,
                            'codigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                            'Des_PrimerNombre' =>  $solicitudUpdate->PrimerNombre,
                            'Des_SegundoNombre' =>  $solicitudUpdate->SegundoNombre,
                            'Des_ApePaterno' =>  $solicitudUpdate->ApellidoPaterno,
                            'Des_AperMaterno' =>  $solicitudUpdate->ApellidoMaterno,
                            'Des_Correo1' =>  $solicitudUpdate->Correo,
                            'Num_DocumentoID' =>  $solicitudUpdate->DocumentoID,
                            'IdTipoPersona'=>  $solicitudUpdate->IdTipoPersona,
                            'Flg_Estado'=>  1,
                            'FechaCreacion'=>  $date,
                            'UsuarioCreacion'=>  $user->NUsuario
                        ]);
                    
                        UsuarioModel::create([
                            'IdUsuario' => $id,
                            'IdPersonal' => $idp,
                            'NUsuario' =>  $usr,
                            'NContrasenia' =>  $pass,
                            'Flg_Estado' =>  1,
                            'FechaCreacion'=>  $date,
                            'UsuarioCreacion'=>  $user->Nusuario,
                            'Flg_TipoUsuario'=>  $solicitudUpdate->IdTipoPersona,
                            'UsuarioCreacion'=>  $user->NUsuario
                        ]);

                        $persona = PersonaModel::where('Num_DocumentoID', trim($solicitudUpdate->DocumentoID))->first();
                        $idper = $persona->IdPersonal;
                        $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                        $personaBroker = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                        $personaBroker = PersonaModel::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                        $idperpad = $personaBroker->IdPersonal;
                        $idperpad = str_pad(intval($idperpad),8,"0",STR_PAD_LEFT);
                        
                        $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                        PersonaRelacionHistModel::create([
                            'IdPersonaRelacion' => $idprh,
                            'IdPersonal' => $idper,
                            'IdPersonalPadre' => $idperpad,
                            'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                            'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                            'Fec_Inicio' =>  $date,
                            'Fec_Fin' =>  $datefin,
                            'Flg_EstadoAfiliado' =>  1,
                            'FechaCreacion'=>  $date,
                            'UsuarioCreacion'=>  $user->NUsuario
                        ]);

                        $usuarioUpdate = (object) $usuarioUpdate = [ 
                            'name' => $usr,
                            'pass' => $passMail,
                            'ustype' => $solicitudUpdate->IdTipoPersona,
                            'fullname' => $nombreCompleto
                        ];

                        $for = trim($agente->Correo);

                        Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($for){
                             $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                            $msj->subject('Bienvenido a Wlinii');
                            $msj->to($for);
                        });
                    }
                    else{
                        // validar si ya esta en empresa
                        $persona = PersonaModel::where('Num_DocumentoID', trim($solicitudUpdate->DocumentoID))->first();

                        $afiliado = PersonaRelacionHistModel::where('IdPersonal', trim($persona->IdPersonal))->where('Flg_EstadoAfiliado', 1)->first();
                        if(!$afiliado){
                            $idper = $persona->IdPersonal;
                            $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                            $personaBroker = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                            $personaBroker = PersonaModel::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                            $idperpad = $personaBroker->IdPersonal;
                            $idperpad = str_pad(intval($idper),8,"0",STR_PAD_LEFT);

                            $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                            PersonaRelacionHistModel::create([
                                'IdPersonaRelacion' => $idprh,
                                'IdPersonal' => $idper,
                                'IdPersonalPadre' => $idperpad,
                                'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                                'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                                'Fec_Inicio' =>  $date,
                                'Fec_Fin' =>  $datefin,
                                'Flg_EstadoAfiliado' =>  1,
                                'FechaCreacion'=>  $date,
                                'UsuarioCreacion'=>  $user->NUsuario
                            ]);
                        }
                        else{ 
                            return redirect()->back()->with('status', 'Usuario ya registrado a una empresa.');
                        }
                    }  
                }
                else{
                    if($solicitudUpdate->CodigoMVCS){
                        $agente = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                        $usuario = UsuarioModel::where('NUsuario', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                        
                        if($agente && !$usuario){
                            
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
                        else{ 
                            $afiliado = PersonaRelacionHistModel::where('CodigoMVCS', $solicitudUpdate->CodigoMVCS)->where('Flg_EstadoAfiliado', 1)->first();
                            if(!$afiliado){
                                $persona = PersonaModel::where('Num_DocumentoID', trim($solicitudUpdate->DocumentoID))->first();
                                if($persona){ 
                                    $idper = $persona->IdPersonal;
                                    $idper = str_pad(intval($idper),8,"0",STR_PAD_LEFT);
                                    $personaBroker = AgenteModel::where('CodigoMVCS', trim($solicitudUpdate->CodigoMVCSPadre))->first();
                                    $personaBroker = PersonaModel::where('Num_DocumentoID', trim($personaBroker->NumDocumento))->first();
                                    $idperpad = $personaBroker->IdPersonal;
                                    $idperpad = str_pad(intval($idper),8,"0",STR_PAD_LEFT);

                                    $datefin =  $date = date("Y-m-d H:i:s",strtotime('+1 year')).".".$ms;
                                    PersonaRelacionHistModel::create([
                                        'IdPersonaRelacion' => $idprh,
                                        'IdPersonal' => $idper,
                                        'IdPersonalPadre' => $idperpad,
                                        'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                                        'CodigoMVCS' =>  $solicitudUpdate->CodigoMVCS,
                                        'Fec_Inicio' =>  $date,
                                        'Fec_Fin' =>  $datefin,
                                        'Flg_EstadoAfiliado' =>  1,
                                        'FechaCreacion'=>  $date,
                                        'UsuarioCreacion'=>  $user->NUsuario
                                    ]);
                                    UsuarioModel::where('NUsuario', $solicitudUpdate->CodigoMVCS)
                                    ->update([ 
                                        'Flg_Estado' =>  1
                                    ]);
                                    return redirect()->route('dashboard')->with('status', 'Se afilio y se envio correo del usuario');
                                }
                                else{
                                    return redirect()->back()->with('status', 'Usuario no registrado en wlinii.');
                                }
                            }
                            else{
                                // if($afiliado->Flg_EstadoAfiliado!=1){
                                //     PersonaRelacionHistModel::where('CodigoMVCS', $solicitudUpdate->CodigoMVCS)
                                //     ->update([ 
                                //         'Flg_EstadoAfiliado' =>  1, 
                                //         'CodigoMVCSPadre' => $solicitudUpdate->CodigoMVCSPadre,
                                //         'FechaModificacion'=>  $date,
                                //         'UsuarioModificacion'=>  $user->NUsuario
                                //     ]);
                                //     return redirect()->route('dashboard')->with('status', 'Se afilio y se envio correo del usuario');
                                // }
                                return redirect()->back()->with('status', 'Usuario ya afiliado a una empresa.');
                            }
                        }
                    }

                }

            }
            return redirect()->route('dashboard')->with('status', 'Se autorizo y se envio correo del usuario');
        }
        return redirect()->route('dashboard')->with('message', 'La solicitud ha sido rechazada');
    }
}