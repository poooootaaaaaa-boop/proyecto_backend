<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'clinica_id',
        'puesto_id',
        'numero_empleado',
        'nombre',
        'apellidos',
        'correo',
        'telefono',
        'fecha_nacimiento',
        'curp',
        'rfc',
        'nss',
        'direccion',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'fecha_ingreso',
        'tipo_contrato',
        'salario_mensual',
        'estado',
        'fecha_baja',
        'motivo_baja',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date:Y-m-d',
        'fecha_ingreso'    => 'date:Y-m-d',
        'fecha_baja'       => 'date:Y-m-d',
        'salario_mensual'  => 'decimal:2',
    ];

    protected $appends = ['nombre_completo', 'antiguedad_anios'];

    /* ---------------- Relaciones ---------------- */

    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioEmpleado::class, 'empleado_id')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio');
    }

    public function permisos()
    {
        return $this->hasMany(PermisoEmpleado::class, 'empleado_id');
    }

    public function vacaciones()
    {
        return $this->hasMany(VacacionEmpleado::class, 'empleado_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialLaboral::class, 'empleado_id');
    }

    /* ---------------- Accesores ---------------- */

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . $this->apellidos);
    }

    /** Años completos de antigüedad (hasta hoy, o hasta la baja si ya no labora). */
    public function getAntiguedadAniosAttribute(): int
    {
        return $this->aniosCumplidosAl(
            $this->estado === 'baja' && $this->fecha_baja ? $this->fecha_baja : Carbon::today()
        );
    }

    /* ---------------- Reglas de negocio ---------------- */

    public function aniosCumplidosAl($fecha): int
    {
        if (!$this->fecha_ingreso) {
            return 0;
        }

        $fecha = Carbon::parse($fecha)->startOfDay();
        $ingreso = Carbon::parse($this->fecha_ingreso)->startOfDay();

        if ($fecha->lt($ingreso)) {
            return 0;
        }

        return $ingreso->diff($fecha)->y;
    }

    /**
     * Días de vacaciones por antigüedad según la Ley Federal del Trabajo
     * (art. 76, reforma "Vacaciones Dignas", vigente desde 2023):
     * 1 año = 12 días, +2 por año hasta el 5º (20 días);
     * después +2 cada 5 años (6-10 = 22, 11-15 = 24, ...).
     * Menos de 1 año cumplido = 0 (aún no genera el derecho).
     */
    public static function diasVacacionesPorAnios(int $anios): int
    {
        if ($anios < 1) {
            return 0;
        }

        if ($anios <= 5) {
            return 12 + ($anios - 1) * 2;
        }

        return 22 + 2 * intdiv($anios - 6, 5);
    }

    /**
     * ¿Tiene ya un permiso o vacaciones (pendiente/aprobado) que se traslape con el rango?
     * Devuelve 'permiso' | 'vacaciones' | null.
     */
    public function ausenciaEnRango($inicio, $fin): ?string
    {
        $traslapa = fn ($q) => $q->whereIn('estado', ['pendiente', 'aprobado'])
            ->whereDate('fecha_inicio', '<=', $fin)
            ->whereDate('fecha_fin', '>=', $inicio);

        if ($traslapa($this->permisos())->exists()) {
            return 'permiso';
        }

        if ($traslapa($this->vacaciones())->exists()) {
            return 'vacaciones';
        }

        return null;
    }

    /** Registra un evento en el historial laboral. */
    public function registrarHistorial(string $tipo, string $descripcion, array $extra = []): HistorialLaboral
    {
        return $this->historial()->create(array_merge([
            'tipo'        => $tipo,
            'fecha'       => Carbon::today()->toDateString(),
            'descripcion' => mb_substr($descripcion, 0, 500),
            'automatico'  => true,
        ], $extra));
    }
}