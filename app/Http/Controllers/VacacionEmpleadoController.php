<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResuelveSolicitudes;
use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\Empleado;
use App\Models\VacacionEmpleado;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CLIC-389 · Registrar vacaciones */
class VacacionEmpleadoController extends Controller
{
    use ScopesPorClinica, ResuelveSolicitudes;

    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $query = VacacionEmpleado::with('empleado.puesto:id,nombre')
            ->whereHas('empleado', fn ($q) => $q->where('clinica_id', $clinicaId));

        foreach (['estado', 'empleado_id'] as $filtro) {
            if ($request->filled($filtro)) {
                $query->where($filtro, $request->input($filtro));
            }
        }

        return response()->json(
            $query->orderByDesc('fecha_inicio')->orderByDesc('id')->get()
        );
    }

    /** Saldo de vacaciones del periodo vigente del empleado. */
    public function saldo(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);

        $periodo = $empleado->aniosCumplidosAl(Carbon::today());

        return response()->json($this->saldoPeriodo($empleado, $periodo) + [
            'fecha_ingreso'        => $empleado->fecha_ingreso->toDateString(),
            'proximo_aniversario'  => $empleado->fecha_ingreso->copy()->addYears($periodo + 1)->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $data = $request->validate([
            'empleado_id'  => ['required', 'integer',
                Rule::exists('empleados', 'id')->where('clinica_id', $clinicaId)],
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'comentarios'  => 'nullable|string|max:1000',
        ], $this->mensajes(), $this->atributos());

        $empleado = Empleado::with('horarios')->findOrFail($data['empleado_id']);

        if ($empleado->estado !== 'activo') {
            return response()->json(['message' => 'El empleado está dado de baja.'], 422);
        }

        if (Carbon::parse($data['fecha_inicio'])->lt($empleado->fecha_ingreso)) {
            return response()->json(['message' => 'Las vacaciones no pueden iniciar antes de la fecha de ingreso.'], 422);
        }

        $dias = $this->diasLaborables($empleado, $data['fecha_inicio'], $data['fecha_fin']);

        if ($dias === 0) {
            return response()->json(['message' => 'El rango seleccionado no incluye días laborables del empleado.'], 422);
        }

        $periodo = $empleado->aniosCumplidosAl($data['fecha_inicio']);
        $saldo   = $this->saldoPeriodo($empleado, $periodo);

        if ($saldo['derecho'] === 0) {
            return response()->json([
                'message' => 'El empleado aún no cumple 1 año de antigüedad en la fecha de inicio, por lo que no tiene vacaciones disponibles.',
            ], 422);
        }

        if ($dias > $saldo['disponibles']) {
            return response()->json([
                'message' => "Saldo insuficiente: solicita {$dias} día(s) laborables y solo tiene {$saldo['disponibles']} disponibles "
                           . "(derecho {$saldo['derecho']}, aprobados {$saldo['tomados']}, pendientes {$saldo['pendientes']}).",
            ], 422);
        }

        if ($choque = $empleado->ausenciaEnRango($data['fecha_inicio'], $data['fecha_fin'])) {
            return response()->json([
                'message' => "El empleado ya tiene un registro de {$choque} que se traslapa con esas fechas.",
            ], 422);
        }

        $vacacion = VacacionEmpleado::create($data + [
            'periodo' => $periodo,
            'dias'    => $dias,
            'estado'  => 'pendiente',
        ]);

        return response()->json([
            'message'  => "Vacaciones registradas ({$dias} día(s) laborables), quedan pendientes de aprobación",
            'vacacion' => $vacacion->load('empleado.puesto:id,nombre'),
        ], 201);
    }

    /** Body: { clinica_id, estado: aprobado|rechazado|cancelado, observaciones?, registrado_por? } */
    public function resolver(Request $request, VacacionEmpleado $vacacion)
    {
        $vacacion->loadMissing('empleado');

        $desc = "Vacaciones aprobadas del {$vacacion->fecha_inicio->format('d/m/Y')} al "
              . "{$vacacion->fecha_fin->format('d/m/Y')} ({$vacacion->dias} día(s) laborables).";

        return $this->resolverSolicitud($request, $vacacion, 'vacaciones', $desc);
    }

    /* ------------------------------------------------------------ */

    private function saldoPeriodo(Empleado $empleado, int $periodo): array
    {
        $derecho    = Empleado::diasVacacionesPorAnios($periodo);
        $tomados    = (int) $empleado->vacaciones()->where('periodo', $periodo)->where('estado', 'aprobado')->sum('dias');
        $pendientes = (int) $empleado->vacaciones()->where('periodo', $periodo)->where('estado', 'pendiente')->sum('dias');

        return [
            'periodo'              => $periodo,
            'antiguedad_anios'     => $empleado->antiguedad_anios,
            'derecho'              => $derecho,
            'tomados'              => $tomados,
            'pendientes'           => $pendientes,
            'disponibles'          => max(0, $derecho - $tomados - $pendientes),
        ];
    }

    /**
     * Días laborables del rango según el horario del empleado.
     * Si todavía no tiene horario, se asume lunes a viernes.
     */
    private function diasLaborables(Empleado $empleado, string $inicio, string $fin): int
    {
        $dias = $empleado->horarios->where('activo', true)->pluck('dia_semana')->unique()->all();

        if (empty($dias)) {
            $dias = [1, 2, 3, 4, 5];
        }

        $total = 0;
        foreach (CarbonPeriod::create($inicio, $fin) as $dia) {
            if (in_array($dia->dayOfWeekIso, $dias, true)) {
                $total++;
            }
        }

        return $total;
    }
}