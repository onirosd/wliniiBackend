<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SolicitudModel extends Model
{
    protected $table = "bdsolicitudes"; 
    
    public function getKeyName(){
        return "IdbdSolicitudes";
    }

    public $timestamps = false;
}
