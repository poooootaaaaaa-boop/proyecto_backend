<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use App\Models\Especialidad;
Use App\Models\asistencias;


class Doctor extends Model
{
    protected $table = 'doctores';

    protected $fillable = [
        'usuario_id',
        'clinica_id',
        'especialidad_id',
        'cedula_profesional',
        'anios_exp',
        'telefono'
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function clinica()
    {
        return $this->belongsTo(Clinica::class);
    }

    public function especialidad()
{
    return $this->belongsTo(Especialidad::class, 'especialidad_id');
}

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'doctor_id');
    }

        public function horarios()
{
    return $this->hasMany(
        HorarioDoctor::class,
        'doctor_id'
    );
}

public function consultorios()
{
    return $this->hasMany(
        DoctorConsultorio::class,
        'doctor_id'
    );
}
// Añade esta relación al final de tu modelo DoctorModel:
public function asistencias()
{
    return $this->hasMany(App\Models\asistencia::class, 'doctor_id');
}
}