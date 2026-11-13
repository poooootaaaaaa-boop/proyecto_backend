<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionDoctor extends Model
{
      protected $table = 'sesiones_doctores';

    protected $fillable = [
        'usuario_id',
        'token_id',
        'inicio_sesion',
        'fin_sesion',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'inicio_sesion' => 'datetime',
            'fin_sesion' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
