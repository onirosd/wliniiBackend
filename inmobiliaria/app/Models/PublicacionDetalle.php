<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicacionDetalle extends Model
{
    //
    protected $table = 'publicaciondetalleestados';
    protected $primaryKey = 'IdPubDetalle';
    public $incrementing = false;
    public $timestamps = false;

    public function state(){
        return $this->belongsTo('App\Models\EstadoPublicacion', 'Id_EstadoPublicacion');
    }
}
