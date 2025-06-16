<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'usuario',
        'password',
        'nivel',
        'nombre',
        'estado',
        'status'
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected $casts = [
        'nivel' => 'integer',
        'estado' => 'integer',
        'status' => 'integer',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'usuario' => $this->usuario,
            'nivel' => $this->nivel,
            'nombre' => $this->nombre
        ];
    }
}
