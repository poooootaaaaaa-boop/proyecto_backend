<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OcupacionHabitacion extends Model
{
    protected $table = 'ocupacion_habitaciones';

    public $timestamps = false;

    protected $fillable = [
        'habitacion_id',
        'paciente_id',
        'fecha_ingreso',
        'fecha_salida',
        'estado',
        'motivo',       // <-- agregado
        'diagnostico',  // <-- agregado
        'notas_alta',   // <-- agregado
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_salida' => 'datetime',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // <-- agregado: esto es lo que causaba el BadMethodCallException
    public function scopeActivas($query)
    {
        return $query->where('estado', 'Activa');
    }
}