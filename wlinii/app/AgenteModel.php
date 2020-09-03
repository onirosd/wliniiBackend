<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgenteModel extends Model
{
    protected $table = "BDAgentesServicio"; 
    
    public function getKeyName(){
        return "IdbdAgente";
    }

    public $timestamps = false;
}
