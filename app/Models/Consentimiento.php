<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consentimiento extends Model
{
    use HasFactory;

    protected $table = 'consentimientos';

    protected $fillable = [
        'paciente_id',
        'doctor_id',
        'formato_id',
        'consulta_id',
        'titulo',
        'contenido',
        'firma',
        'pdf',
        'estado',
        'fecha_firma',
        'observaciones'
    ];

    protected $casts = [
        'fecha_firma' => 'datetime'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function formato()
    {
        return $this->belongsTo(FormatoConsentimiento::class, 'formato_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function adjuntos()
    {
        return $this->hasMany(ConsentimientoAdjunto::class, 'consentimiento_id');
    }

    public function historial()
    {
        return $this->hasMany(ConsentimientoHistorial::class, 'consentimiento_id');
    }
}