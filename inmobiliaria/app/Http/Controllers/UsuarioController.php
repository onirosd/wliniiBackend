<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\PersonaRelacionHist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UsuarioController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
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

    // LOGIN
    public function login(Request $request)
    {       
        // echo $password = Hash::make('123123'); 
        $password = $request->input('password');
        $usuario  = $request->input('user');
        $data     = $request->json()->all();

        if(count($data)==2){
            $user     =  Usuario::where('NUsuario', $usuario)->where('Flg_Estado', 1)->first();
            $afiliado = PersonaRelacionHist::where('IdPersonal', $user->IdPersonal)->where('Flg_EstadoAfiliado', 1)->first();
            if($user){ 
                if (Hash::check($password, trim($user->NContrasenia))){
                    //if($afiliado){ //AND $user->Flg_TipoUsuario==1){
                        try {
                            if (! $token = $this->jwt->fromUser($user)) {
                                return response()->json(['user_not_found'], 404);
                            }
                        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                
                            return response()->json(['token_expired'], 500);
                
                        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                
                            return response()->json(['token_invalid'], 500);
                
                        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
                
                            return response()->json(['token_absent' => $e->getMessage()], 500);
                
                        }

						$activity = round(microtime(true)*1000);
                        $user->access_token = $token;
                        $user->last_activity = $activity;
                        $user->save();
                        $persona = DB::table('persona')->where('IdPersonal', $user->IdPersonal)->first();

                        return response()->json([
                            'username' => $user->NUsuario,
                            'role' => $user->Flg_TipoUsuario,
                            'avatar' => $persona ? $persona->Img_Personal : null,
                            'access_token' => $token,
                        ],201);
                    //}
                    //else{
                       // return response()->json(['message' => 'El usuario no esta afiliado a una empresa.'],401);
                    //}
                }
                else{
                    return response()->json(['error' => 'El password es incorrecto.'],401);
                }
            }
            else{
                return response()->json(['error' => 'El usuario esta inactivo.'],401);
            }
        }
        else{
            return response()->json(['error' => 'El número de campos es incorrecto.'],401);
        }
    }  



    public function recuperarPassword(Request $request){
        $user = $this->authUser();

        $this->validate($request, [
            'correo' => 'required'
        ]);

        $correo      = $request->input('correo');
        $persona     =  Persona::where('Des_Correo1', ''.$correo.'')->first();
        
        if( count((array)$persona) == 0){

           return response()->json(['error' => 'Correo Electronico Invalido'],401);
        }

        $usuario     =  Usuario::where('IdPersonal', $persona->IdPersonal)->first();

        $longitud = 5;
        $caracteres = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $cadenaAleatoria = '';
        for ($i = 0; $i < $longitud; $i++) {

             $cadenaAleatoria .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        // $cadenaAleatoria;

        $passMail = $cadenaAleatoria;
        $pass = Hash::make($cadenaAleatoria);

         Usuario::where('IdUsuario', $usuario->IdUsuario)
                    ->update([
                        'NContrasenia' => $pass
                    ]);



        $usuarioUpdate = (object) $usuarioUpdate = [ 
                            'user' => $usuario->NUsuario,
                            'pass' => $passMail,
                            'name' => $persona->Des_NombreCompleto,
                            'ustype' => 'recuperar'
                        ];

    
                 

       Mail::send('email',['user'=>$usuarioUpdate], function($msj) use($correo){
                            $msj->from($_ENV['MAIL_USERNAME'], 'wlinii');
                            $msj->subject('Recuperar Contraseña');
                            $msj->to($correo);
       });

       return response()->json([
                'status'  => 'success',
                'message' => 'Se enviaron las credenciales actualizadas a su usuario'
       ]);



} 

    public function changePassword(Request $request){
        $user = $this->authUser();

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword' => 'required',
            'confirmpassword' => 'required|same:newpassword'
        ]);
        
        $oldPassword = $request->input('currentpassword');
        $newPassword = $request->input('newpassword');

        if(Hash::check($oldPassword, trim($user->NContrasenia))){
            Usuario::where('IdUsuario', $user->IdUsuario)
                    ->update([
                        'NContrasenia' => Hash::make($newPassword)
                    ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Password is updated successfully.'
            ]);
        }else{
            return response()->json([
                'status' => 'fail',
                'message' => 'password is not correct.'
            ], 400);
        }
    }

    // CREATE
    public function crearUsuario(Request $request)
    {
        if($request->isJson()){
            $data = $request->json()->all();
            $usuario = Usuario::create($data);
            return response()->json($usuario, 201);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }

    //READ ALL
    public function mostrarUsuarios(Request $request)
    {
        if($request->isJson()){
            $usuarios = Usuario::All();
            return response()->json($usuarios,200);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }
    //READ ONE
    public function mostrarUsuario($id, Request $request)
    {
        if($request->isJson()){
            $usuario = Usuario::findOrFail($id);
            return response()->json($usuario);
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
    public function borrarUsuario($id, Request $request)
    {
        if($request->isJson()){
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();
            return response()->json(['msj' => 'Usuario borrado correctamente']);
        }
        return response()->json(['error' => 'No autorizado'],401);
    }
}
