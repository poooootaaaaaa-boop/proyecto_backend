<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoEmpleado extends Model
{
    protected $table = 'permisos_empleados';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'con_goce_sueldo',
        'motivo',
        'estado',
        'resuelto_por',
        'resuelto_en',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio'    => 'date:Y-m-d',
        'fecha_fin'       => 'date:Y-m-d',
        'con_goce_sueldo' => 'boolean',
        'resuelto_en'     => 'datetime',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function resolutor()
    {
        return $this->belongsTo(Usuario::class, 'resuelto_por');
    }
}