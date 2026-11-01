<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstanciaConsumo extends Model
{
    protected $table = 'estancia_consumos';

    protected $fillable = [
        'ocupacion_id',
        'paciente_id',
        'doctor_id',
        'insumo_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'observaciones',
        'fecha_hora',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'fecha_hora' => 'datetime',
    ];

    public function ocupacion()
    {
        return $this->belongsTo(OcupacionHabitacion::class, 'ocupacion_id');
    }

    public function insumo()
    {
        return $this->belongsTo(InsumoEstancia::class, 'insumo_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }
}