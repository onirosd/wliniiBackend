<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuarioModel extends Model
{
    protected $table = "usuario"; 
    
    public function getKeyName(){
        return "IdUsuario";
    }
    
    protected $fillable = ['IdUsuario','IdPersonal','NUsuario','NContrasenia','Flg_Estado','FechaModificacion','FechaCreacion','UsuarioCreacion','UsuarioModificacion','Flg_TipoUsuario'];

    public $timestamps = false;
}
