<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; //  IMPORTANTE
use App\Models\Notificacion;
use App\Models\UsuarioNotificacion;
use App\Models\Suscripciones;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable; //  AQUI TAMBIÉN

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'password',
        'rol_id',
        'activo'
    ];

    protected $hidden = [
        'password'
    ];

    public $timestamps = false;

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
        public function paciente()
    {
        return $this->hasOne(Paciente::class, 'usuario_id');
    }
    public function notificaciones()
{
    return $this->belongsToMany(
        Notificacion::class,
        'usuario_notificaciones',
        'usuario_id',
        'notificacion_id'
    )->withPivot('habilitado')->withTimestamps();
}

public function doctor()
{
    return $this->hasOne(Doctor::class, 'usuario_id');
}

public function clinica()
{
    return $this->hasOne(Clinica::class, 'usuario_id');
}

public function farmacia()
{
    return $this->hasOne(Farmacia::class, 'usuario_id');
}

//Nuevo
    public function suscripciones()
{
    //antes hasMany, ahora hasOne
    return $this->hasOne(Suscripciones::class, 'usuario_id');
}

    // NUEVO: Relación con el historial de pagos
    public function pagos()
    {
        return $this->hasMany(HistorialPago::class, 'usuario_id');
    }


public function historialConsentimientos()
{
    return $this->hasMany(
        ConsentimientoHistorial::class,
        'usuario_id'
    );
}

}