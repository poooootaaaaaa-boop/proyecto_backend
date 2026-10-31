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
        'motivo',
        'diagnostico',
        'notas_alta',
        // Checklist de cierre de estancia
        'fecha_alta_medica',
        'doctor_alta_id',
        'diagnostico_egreso',
        'tratamiento_egreso',
        'medicamentos_egreso',
        'recomendaciones',
        'proxima_consulta',
        'condicion_egreso',
    ];

    protected $casts = [
        'fecha_ingreso'     => 'datetime',
        'fecha_salida'      => 'datetime',
        'fecha_alta_medica' => 'datetime',
        'proxima_consulta'  => 'date',
    ];

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function doctorAlta()
    {
        return $this->belongsTo(Doctor::class, 'doctor_alta_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'Activa');
    }
}