<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentimientoHistorial extends Model
{
    use HasFactory;

    protected $table = 'consentimiento_historial';

    public $timestamps = false;

    protected $fillable = [
        'consentimiento_id',
        'usuario_id',
        'accion',
        'descripcion',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function consentimiento()
    {
        return $this->belongsTo(
            Consentimiento::class,
            'consentimiento_id'
        );
    }

    public function usuario()
    {
        return $this->belongsTo(
            Usuario::class,
            'usuario_id'
        );
    }
}