<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AltaPaciente;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Receta;
class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'cita_id', 
        'doctor_id',
        'paciente_id',
        'motivo',
        'sintomas',
        'diagnostico',
        'notas_clinicas',
        'examen',
        'fecha_tratamiento'
    ];

    //  Laravel estándar
    public $timestamps = true;

    public function paciente()
    {
        return $this->belongsTo(AltaPaciente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function receta() {
    return $this->hasOne(Receta::class, 'consulta_id');
}

public function consentimientos()
{
    return $this->hasMany(
        Consentimiento::class,
        'consulta_id'
    );
}

}