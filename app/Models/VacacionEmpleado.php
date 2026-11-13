<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VacacionEmpleado extends Model
{
    protected $table = 'vacaciones_empleados';

    protected $fillable = [
        'empleado_id',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'dias',
        'comentarios',
        'estado',
        'resuelto_por',
        'resuelto_en',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date:Y-m-d',
        'fecha_fin'    => 'date:Y-m-d',
        'resuelto_en'  => 'datetime',
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