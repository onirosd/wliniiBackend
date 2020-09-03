<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicacionImage extends Model
{
    //
    protected $table = 'publicaciondetalleimagenes';
    protected $primaryKey = 'IdPubImage';
    // public $incrementing = false;
    public $timestamps = false;
}
