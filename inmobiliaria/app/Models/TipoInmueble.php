<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoInmueble extends Model
{
    //
    protected $table = 'tipoinmueble';
    protected $primaryKey = 'IdTipoInmueble';
    public $timestamps = false;
    public function properties(){
        return $this->belongsToMany('App\Models\Property', 'tipoinmueble_properties', 'IdTipoInmueble', 'IdProperty');
    }
}
