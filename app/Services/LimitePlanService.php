<?php

namespace App\Services;

use App\Exceptions\LimitePlanExcedidoException;
use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Suscripciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LimitePlanService
{
    /**
     * El cupo pertenece al usuario del doctor. Si ese usuario no tiene
     * suscripción vigente, se usa la del usuario dueño de su clínica y el
     * conteo abarca a todos los doctores de esa clínica.
     *
     * @return array{0: Suscripciones, 1: array{tipo: string, doctor_id?: int, clinica_id?: int}}
     */
    public function assertPuedeRegistrarPaciente(int $doctorId): void
    {
        [$suscripcion, $alcance] = $this->resolverAlcance($doctorId);
        $suscripcion = $this->bloquearSuscripcion($suscripcion);

        $this->verificar(
            $this->contarPacientes($alcance),
            $suscripcion->plan->limite_pacientes,
            'pacientes'
        );
    }

    public function assertPuedeCrearReceta(int $doctorId): void
    {
        [$suscripcion, $alcance] = $this->resolverAlcance($doctorId);
        $suscripcion = $this->bloquearSuscripcion($suscripcion);

        $this->verificar(
            $this->contarRecetasDelPeriodo($alcance, $suscripcion),
            $suscripcion->plan->limite_recetas,
            'recetas'
        );
    }

    /**
     * @return array{0: Suscripciones, 1: array{tipo: string, doctor_id?: int, clinica_id?: int}}
     */
    private function resolverAlcance(int $doctorId): array
    {
        $doctor = Doctor::with([
            'usuario.suscripciones.plan',
            'clinica.usuario.suscripciones.plan',
        ])->find($doctorId);

        if (!$doctor) {
            throw new LimitePlanExcedidoException(
                'No hay una suscripción activa para completar esta acción.'
            );
        }

        $suscripcionDoctor = $this->vigente($doctor->usuario?->suscripciones);

        if ($suscripcionDoctor) {
            return [$suscripcionDoctor, [
                'tipo' => 'doctor',
                'doctor_id' => $doctor->id,
            ]];
        }

        $suscripcionClinica = $this->vigente($doctor->clinica?->usuario?->suscripciones);

        if ($suscripcionClinica && $doctor->clinica_id) {
            return [$suscripcionClinica, [
                'tipo' => 'clinica',
                'clinica_id' => $doctor->clinica_id,
            ]];
        }

        throw new LimitePlanExcedidoException(
            'No hay una suscripción activa para completar esta acción.'
        );
    }

    private function vigente(?Suscripciones $suscripcion): ?Suscripciones
    {
        if (!$suscripcion || $suscripcion->estado !== 'activo' || !$suscripcion->plan) {
            return null;
        }

        if ($suscripcion->fecha_fin && Carbon::parse($suscripcion->fecha_fin)->endOfDay()->isPast()) {
            return null;
        }

        return $suscripcion;
    }

    private function bloquearSuscripcion(Suscripciones $suscripcion): Suscripciones
    {
        if (DB::transactionLevel() === 0) {
            throw new \RuntimeException(
                'La validación del plan debe ejecutarse dentro de una transacción.'
            );
        }

        $bloqueada = Suscripciones::with('plan')
            ->whereKey($suscripcion->id)
            ->lockForUpdate()
            ->first();

        if (!$this->vigente($bloqueada)) {
            throw new LimitePlanExcedidoException(
                'No hay una suscripción activa para completar esta acción.'
            );
        }

        return $bloqueada;
    }

    private function contarPacientes(array $alcance): int
    {
        $consulta = Paciente::query();

        if ($alcance['tipo'] === 'clinica') {
            $consulta->whereIn('doctor_id', function ($doctor) use ($alcance) {
                $doctor->select('id')
                    ->from('doctores')
                    ->where('clinica_id', $alcance['clinica_id']);
            });
        } else {
            $consulta->where('doctor_id', $alcance['doctor_id']);
        }

        return $consulta->count();
    }

    private function contarRecetasDelPeriodo(array $alcance, Suscripciones $suscripcion): int
    {
        $consulta = Receta::query();

        if ($alcance['tipo'] === 'clinica') {
            $consulta->whereIn('doctor_id', function ($doctor) use ($alcance) {
                $doctor->select('id')
                    ->from('doctores')
                    ->where('clinica_id', $alcance['clinica_id']);
            });
        } else {
            $consulta->where('doctor_id', $alcance['doctor_id']);
        }

        if ($suscripcion->fecha_inicio) {
            $consulta->where(
                'creado_en',
                '>=',
                Carbon::parse($suscripcion->fecha_inicio)->startOfDay()
            );
        }

        if ($suscripcion->fecha_fin) {
            $consulta->where(
                'creado_en',
                '<=',
                Carbon::parse($suscripcion->fecha_fin)->endOfDay()
            );
        }

        return $consulta->count();
    }

    private function verificar(int $usados, mixed $limite, string $recurso): void
    {
        if ($limite === null || $limite === '') {
            return;
        }

        $limite = (int) $limite;

        if ($usados >= $limite) {
            $etiqueta = $recurso === 'recetas' ? 'recetas' : 'pacientes';

            throw new LimitePlanExcedidoException(
                "Alcanzaste el límite de {$etiqueta} de tu plan.",
                $limite,
                $usados,
                $recurso
            );
        }
    }
}
