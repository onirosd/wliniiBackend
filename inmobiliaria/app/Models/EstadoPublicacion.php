<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoPublicacion extends Model
{
    //
    protected $table = 'estadopublicacion';
    protected $primaryKey = 'Id_EstadoPublicacion';
    public $incrementing = false;
    public $timestamps = false;
}
