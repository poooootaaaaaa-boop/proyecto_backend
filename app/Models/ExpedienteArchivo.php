<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteArchivo extends Model
{
    protected $table = 'expediente_archivos';

    // La tabla solo tiene `creado_en`, no created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'paciente_id',
        'consulta_id',
        'nombre_archivo',
        'url_archivo',
        'tipo',
        'creado_en',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }
}
