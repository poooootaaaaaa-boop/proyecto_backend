<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstanciaRegistro extends Model
{
    protected $table = 'estancia_registros';

    protected $fillable = [
        'ocupacion_id',
        'paciente_id',
        'doctor_id',
        'tipo',
        'descripcion',
        'costo',
        'fecha_hora',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'fecha_hora' => 'datetime',
    ];

    public function ocupacion()
    {
        return $this->belongsTo(OcupacionHabitacion::class, 'ocupacion_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}