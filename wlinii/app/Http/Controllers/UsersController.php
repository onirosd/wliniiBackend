<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $newsList = DB::table('Usuario AS a')
                    ->select('a.NUsuario as usuario', 'a.IdUsuario', 'b.Des_nombrecompleto as nombrecompleto', 'c.descripcion as flg_tipousuario', 'a.flg_estado', 'b.des_correo1')
                    ->join('Persona AS b', 'a.idpersonal','=','b.idpersonal')
                    ->join('TipoPersona AS c', 'a.flg_tipousuario','=','c.idtipopersona')
                    ->where('a.NUsuario','<>', 'Administrador' )
                    ->orderBy('a.FechaCreacion', 'ASC')
                    ->get();


                  

        return view('users', ['newsList' => $newsList]);
    }


    public function usersDetail($id = null)
    {   
        if($id){
            $users = DB::table('Usuario')
                    ->where('IdUsuario', $id)
                    ->first();
        }else{
            $users = null;
        }

        return view('users_details', ['users' => $users]);
    }



      public function saveUsers2(Request $request)
    {   

        echo "veremos que pasa ";
        $IdUsers = $request->input('id');
        $data = array(

            'Flg_Estado' => $request->input('state'),

        );

        if($IdUsers){
            DB::table('usuario')->where('IdUsuario', $IdUsers)
                ->update($data);
        }

        return redirect()->route('users');
    }


}