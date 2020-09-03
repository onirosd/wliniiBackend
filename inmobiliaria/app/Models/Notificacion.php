<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    //
    protected $table = 'Notificaciones';
    protected $primaryKey = 'IdNotificacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [ 'Flg_Leer' => 'boolean' ];

    public function rate(){
        return $this->hasOne('App\Models\Valoracion', 'IdNotificacion');
    }
}

