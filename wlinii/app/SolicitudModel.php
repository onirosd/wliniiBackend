<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudModel extends Model
{
    protected $table = "BDSolicitudes"; 
    
    public function getKeyName(){
        return "IdbdSolicitudes";
    }

    public $timestamps = false;
}
