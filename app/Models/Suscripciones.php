<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripciones extends Model
{
    protected $table = 'suscripciones';
    protected $primaryKey = "id";
    public $timestamps = false;

    // Si quieres que Laravel gestione automáticamente la fecha de creación en 'creado_en':
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // Como no tienes actualizado_en, lo desactivamos

    protected $fillable = [
        'usuario_id',
        'plan_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'creado_en'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function plan()
    {
        return $this->belongsTo(Planes::class, 'plan_id');
    }
}