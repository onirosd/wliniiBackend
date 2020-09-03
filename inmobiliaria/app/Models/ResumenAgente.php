<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumenAgente extends Model
{
    protected $table = 'ResumenAgente';
    protected $primaryKey = 'IdPersonal';
    public $incrementing = false;
    protected $fillable = ['IdPersonal','Num_Transacciones','Num_Activas','Num_canceladas','FechaModificacion','UsuarioModificacion','FechaCreacion','UsuarioCreacion','Des_NombreCompleto','Des_Ciudad','Des_Numero','Des_Email','Num_Valoracion','Des_ComentarioPersona'];
    public $timestamps = false;

    public function persona(){
        return $this->belongsTo('App\Models\Persona', 'IdPersonal');
    }
}