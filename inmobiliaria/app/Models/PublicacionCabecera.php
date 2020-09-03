<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicacionCabecera extends Model
{
    //
    protected $table = 'publicacioncabecera';
    protected $primaryKey = 'IdPubCabecera';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
            'Num_Antiguedad' => 'integer',
            'Num_Habitaciones' => 'integer',
            'Num_HabitacionesServicio' => 'integer',
            'Num_Banios' => 'integer',
            'Num_BaniosVisita' => 'integer',
            'Num_Cochera' => 'integer',
            'Num_Frente' => 'integer',
            'Num_Fondo' => 'integer',
            'Num_AreaTechado' => 'integer',
            'Num_AreaTotal' => 'integer',
            
            

            'Flg_Consultar' => 'boolean',
            'Flg_MostrarDireccion' => 'boolean',
            'Flg_Hall' => 'boolean',
            'Flg_Sala' => 'boolean',
            'Flg_Comedor' => 'boolean',
            'Flg_SalonComedor' => 'boolean',
            'Flg_Cocina' => 'boolean',
            'Flg_Kitchenette' => 'boolean',
            'Flg_Lavadero' => 'boolean',
            'Flg_Terraza' => 'boolean',
            'Flg_Balcon' => 'boolean',
            'Flg_WalkingCloset' => 'boolean',
            'Flg_Guardiania' => 'boolean',
            'Flg_Looby' => 'boolean',
            'Flg_Agua' => 'boolean',
            'Flg_Luz' => 'boolean',
            'Flg_Cable' => 'boolean',
            'Flg_Internet' => 'boolean',
            'Flg_Vigilancia' => 'boolean',
            'Flg_Gas' => 'boolean'
        ];

    public function images(){
        return $this->hasMany('App\Models\PublicacionImage', 'IdPubCabecera');
    }

    public function detail(){
        return $this->hasMany('App\Models\PublicacionDetalle', 'IdPubCabecera');
    }
}

