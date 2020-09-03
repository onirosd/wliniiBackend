<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaModel extends Model
{
    protected $table = "Persona"; 
    
    public function getKeyName(){
        return "IdPersonal";
    }

    protected $fillable = ['IdPersonal','Des_NombreCompleto','Flg_Estado','IdTipoPersona','FechaCreacion','UsuarioCreacion','FechaModificacion','UsuarioModificacion','Des_Telefono1','Des_Telefono2','Num_DocumentoID','Des_Correo1','Des_Correo2','Cod_MVCS','Des_ComentarioPersona','Des_Rs_Facebook','Des_Rs_Twitter','Des_Rs_Linkedin','Des_PrimerNombre','Des_SegundoNombre','Des_ApePaterno','Des_AperMaterno'];


    public $timestamps = false;
}
