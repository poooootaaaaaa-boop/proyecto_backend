<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planes extends Model
{
    protected $table = 'planes';
    protected $primaryKey = "id";
    public $timestamps = false;

    protected $fillable = [
        'slug',
        'nombre',
        'precio',
        'duracion_dias',
        'limite_pacientes',
        'limite_recetas',
    ];

    public function suscripciones()
    {
        return $this->hasMany(Suscripciones::class, 'plan_id');
    }
}