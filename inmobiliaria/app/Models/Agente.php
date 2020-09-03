<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agente extends Model
{
    protected $table = 'BDAgentesServicio';
    protected $primaryKey = 'IdbdAgente';
    public $incrementing = false;
    protected $fillable = ['IdbdAgente','CodigoRegistro','Nombres','NumDocumento','Direccion','Correo','FechaInscripcion','Estado'];
    public $timestamps = false;
}