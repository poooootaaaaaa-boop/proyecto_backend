<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialLaboral extends Model
{
    protected $table = 'historial_laboral';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'fecha',
        'descripcion',
        'valor_anterior',
        'valor_nuevo',
        'automatico',
        'registrado_por',
    ];

    protected $casts = [
        'fecha'      => 'date:Y-m-d',
        'automatico' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function registrador()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}