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
        'dias_estimados',
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
        'receta_id',
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

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

    public function registrosEstancia()
    {
        return $this->hasMany(EstanciaRegistro::class, 'ocupacion_id')->orderBy('fecha_hora');
    }

    public function consumosEstancia()
    {
        return $this->hasMany(EstanciaConsumo::class, 'ocupacion_id')->orderBy('fecha_hora');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'Activa');
    }
}