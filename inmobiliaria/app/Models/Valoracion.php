<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    //
    protected $table = 'Valoraciones';
    public $incrementing = false;
    protected $fillable = ['IdNotificacion','IdPersonal','IdPersonalCalificado','Num_Valoracion','FechaCreacion','FechaModificacion','UsuarioCreacion','UsuarioModificacion'];
    public $timestamps = false;
}

