<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgenteModel extends Model
{
    protected $table = "bdagentesservicio"; 
    
    public function getKeyName(){
        return "IdbdAgente";
    }

    public $timestamps = false;
}
