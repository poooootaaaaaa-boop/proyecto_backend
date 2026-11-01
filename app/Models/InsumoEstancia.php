<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsumoEstancia extends Model
{
    protected $table = 'insumos_estancia';

    protected $fillable = [
        'nombre',
        'categoria',
        'unidad_medida',
        'precio_unitario',
        'activo',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function consumos()
    {
        return $this->hasMany(EstanciaConsumo::class, 'insumo_id');
    }
}