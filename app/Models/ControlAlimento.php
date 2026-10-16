<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlAlimento extends Model
{
     protected $table = 'control_alimentos';

    protected $fillable = [
        'paciente_id',
        'fecha_hora_recepcion',
        'estado_alimentos',
        //'cantidad_recibida',
        'detalle_alimentos',
        'alimentos_desechados',
        'motivo_desecho',
        'paciente_consumio',
        'observaciones_nutricionales',
        'entrega_alimentos_paciente',
    ];

    protected $casts = [
        'fecha_hora_recepcion' => 'datetime',
        //'cantidad_recibida' => 'decimal:2',
        'detalle_alimentos' => 'array',
        'paciente_consumio' => 'boolean',
        'entrega_alimentos_paciente' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}


