<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $table = 'puestos';

    protected $fillable = [
        'clinica_id',
        'nombre',
        'descripcion',
        'salario_base',
        'activo',
    ];

    protected $casts = [
        'activo'       => 'boolean',
        'salario_base' => 'decimal:2',
    ];

    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'puesto_id');
    }
}