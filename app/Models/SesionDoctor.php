<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class, 'usuario_id', 'usuario_id');
    }

    public function scopeFiltrar(Builder $consulta, array $filtros): Builder
    {
        $consulta->whereHas('usuario', function (Builder $usuario) {
            $usuario->where('rol_id', 3);
        });

        if (!empty($filtros['desde'])) {
            $consulta->where(
                'inicio_sesion',
                '>=',
                Carbon::parse($filtros['desde'])->startOfDay()
            );
        }

        if (!empty($filtros['hasta'])) {
            $consulta->where(
                'inicio_sesion',
                '<=',
                Carbon::parse($filtros['hasta'])->endOfDay()
            );
        }

        if (!empty($filtros['doctor_id'])) {
            $consulta->whereHas('doctor', function (Builder $doctor) use ($filtros) {
                $doctor->where('id', $filtros['doctor_id']);
            });
        }

        if (!empty($filtros['usuario_id'])) {
            $consulta->where('usuario_id', $filtros['usuario_id']);
        }

        return $consulta;
    }
}
