<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Usuario extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
	use Authenticatable, Authorizable;

    protected $table = 'Usuario';
    protected $primaryKey = 'IdUsuario';
    public $incrementing = false;
    protected $fillable = ['IdUsuario','IdPersonal','NUsuario','NContrasenia','Flg_Estado','FechaModificacion','FechaCreacion','UsuarioCreacion','UsuarioModificacion','Flg_TipoUsuario'];
    protected $hidden = ['NContrasenia'];
    public $timestamps = false;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}