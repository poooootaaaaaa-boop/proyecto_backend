<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'usuario_id',
        'doctor_id',
        'nacimiento',
        'genero',
        'tipoSangre',
        'alergias',
        'padecimientoHeredofamiliar',
        'direccion',
        'creado_en'
    ];

    public $timestamps = false;

    // ============================
    // RELACIONES
    // ============================

    // Paciente pertenece a Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // Paciente pertenece a Doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    // Paciente tiene muchas citas
    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    // Paciente tiene muchas consultas
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'paciente_id');
    }

    public function consentimientos()
{
    return $this->hasMany(
        Consentimiento::class,
        'paciente_id'
    );
}
   
}