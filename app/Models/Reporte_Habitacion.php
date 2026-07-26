<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte_Habitacion extends Model
{
    // Indicamos explícitamente la tabla a la que apunta este modelo
    protected $table = 'reporte__habitacions';

    // Definimos los campos que se pueden llenar de forma masiva
    protected $fillable = [
        'cuarto_id',
        'instrumento_id',
        'descripcion',
        'foto',
    ];
}
