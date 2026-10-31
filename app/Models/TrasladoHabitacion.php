<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrasladoHabitacion extends Model
{
    protected $table = 'traslados_habitacion';

    protected $fillable = [
        'ocupacion_id',
        'habitacion_origen_id',
        'habitacion_destino_id',
        'doctor_id',
        'motivo',
        'fecha_traslado',
    ];

    public function ocupacion()
    {
        return $this->belongsTo(OcupacionHabitacion::class, 'ocupacion_id');
    }

    public function habitacionOrigen()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_origen_id');
    }

    public function habitacionDestino()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_destino_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}