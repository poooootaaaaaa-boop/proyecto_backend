<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** CLIC-387 · Registrar horarios */
class HorarioEmpleadoController extends Controller
{
    use ScopesPorClinica;

    /** Empleados activos de la clínica con su horario semanal. */
    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $empleados = Empleado::with(['puesto:id,nombre', 'horarios'])
            ->where('clinica_id', $clinicaId)
            ->where('estado', 'activo')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->get();

        return response()->json($empleados);
    }

    /**
     * Reemplaza TODO el horario semanal del empleado.
     * Body: { clinica_id, horarios: [{dia_semana:1-7, hora_inicio:"08:00", hora_fin:"16:00"}, ...] }
     * Se permiten turnos nocturnos (fin menor que inicio) y varios bloques por día sin traslapes.
     */
    public function sync(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);

        if ($empleado->estado !== 'activo') {
            return response()->json(['message' => 'No se puede editar el horario de un empleado dado de baja.'], 422);
        }

        $data = $request->validate([
            'horarios'               => 'present|array|max:42',
            'horarios.*.dia_semana'  => 'required|integer|between:1,7',
            'horarios.*.hora_inicio' => 'required|date_format:H:i',
            'horarios.*.hora_fin'    => 'required|date_format:H:i',
        ], $this->mensajes(), $this->atributos());

        $porDia = [];
        foreach ($data['horarios'] as $h) {
            $ini = $this->minutos($h['hora_inicio']);
            $fin = $this->minutos($h['hora_fin']);

            if ($ini === $fin) {
                return response()->json(['message' => 'La hora de inicio y fin no pueden ser iguales.'], 422);
            }

            if ($fin < $ini) {
                $fin += 1440; // turno nocturno: termina al día siguiente
            }

            foreach ($porDia[$h['dia_semana']] ?? [] as [$i2, $f2]) {
                if ($ini < $f2 && $i2 < $fin) {
                    return response()->json(['message' => 'Hay horarios traslapados en un mismo día.'], 422);
                }
            }

            $porDia[$h['dia_semana']][] = [$ini, $fin];
        }

        DB::transaction(function () use ($empleado, $data) {
            $empleado->horarios()->delete();

            foreach ($data['horarios'] as $h) {
                $empleado->horarios()->create([
                    'dia_semana'  => $h['dia_semana'],
                    'hora_inicio' => $h['hora_inicio'],
                    'hora_fin'    => $h['hora_fin'],
                    'activo'      => true,
                ]);
            }
        });

        return response()->json([
            'message'  => 'Horario guardado',
            'horarios' => $empleado->horarios()->get(),
        ]);
    }

    private function minutos(string $hhmm): int
    {
        [$h, $m] = array_map('intval', explode(':', $hhmm));

        return $h * 60 + $m;
    }
}