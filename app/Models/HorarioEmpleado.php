<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioEmpleado extends Model
{
    protected $table = 'horarios_empleados';

    protected $fillable = [
        'empleado_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'activo'     => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}