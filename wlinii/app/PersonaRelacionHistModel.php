<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaRelacionHistModel extends Model
{
    protected $table = 'PersonaRelacion_Hist';
    protected $primaryKey = 'IdPersonaRelacion';
    public $incrementing = false;
    protected $fillable = ['IdPersonaRelacion','CodigoMVCSPadre','CodigoMVCS','Fec_Inicio','Fec_Fin','Flg_EstadoAfiliado','FechaCreacion','UsuarioCreacion','FechaModificacion','UsuarioModificacion','IdPersonal','IdPersonalPadre'];
    public $timestamps = false;
}
