<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'bdsolicitudes';
    protected $primaryKey = 'IdbdSolicitudes';
    public $incrementing = false;
    protected $fillable = ['IdbdSolicitudes','IdTipoPersona','IdPersonal','Telefono','Correo','Mensaje','Estado','PrimerNombre','SegundoNombre','ApellidoPaterno','ApellidoMaterno','DocumentoID','CodigoMVCS','CodigoMVCSPadre','FechaCreacion'];
    public $timestamps = false;
}