<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaRelacionHistModel extends Model
{
    protected $table = 'personarelacion_hist';
    protected $primaryKey = 'IdPersonaRelacion';
    public $incrementing = false;
    protected $fillable = ['IdPersonaRelacion','CodigoMVCSPadre','CodigoMVCS','Fec_Inicio','Fec_Fin','Flg_EstadoAfiliado','FechaCreacion','UsuarioCreacion','FechaModificacion','UsuarioModificacion','IdPersonal','IdPersonalPadre'];
    public $timestamps = false;
}
