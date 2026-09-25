<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\OcupacionHabitacion;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HospitalizacionController extends Controller
{
    /**
     * GET /api/hospitalizaciones/activas?buscar=
     * Pacientes actualmente hospitalizados (para la tabla principal).
     */
    public function activas(Request $request)
    {
        $query = OcupacionHabitacion::activas()
            ->with(['paciente.usuario', 'habitacion'])
            ->orderByDesc('fecha_ingreso');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('paciente.usuario', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            });
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * POST /api/hospitalizaciones
     * 1) Registra fecha/hora de entrada  2) Asigna la habitación al paciente.
     */
    public function ingresar(Request $request)
    {
        $validado = Validator::make($request->all(), [
            'paciente_id'   => 'required|exists:pacientes,id',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'fecha_ingreso' => 'required|date',
            'motivo'        => 'nullable|string|max:255',
            'diagnostico'   => 'nullable|string|max:255',
        ])->validate();

        $yaHospitalizado = OcupacionHabitacion::activas()
            ->where('paciente_id', $validado['paciente_id'])
            ->exists();

        if ($yaHospitalizado) {
            return response()->json([
                'message' => 'Este paciente ya tiene una hospitalización activa.',
            ], 422);
        }

        $habitacion = Habitacion::findOrFail($validado['habitacion_id']);
        if ($habitacion->estado !== 'Disponible') {
            return response()->json([
                'message' => 'La habitación seleccionada ya no está disponible.',
            ], 422);
        }

        $ocupacion = DB::transaction(function () use ($validado, $habitacion) {
            $registro = OcupacionHabitacion::create([
                'paciente_id'   => $validado['paciente_id'],
                'habitacion_id' => $validado['habitacion_id'],
                'fecha_ingreso' => $validado['fecha_ingreso'],
                'fecha_salida'  => null,
                'estado'        => 'Activa',
                'motivo'        => $validado['motivo'] ?? null,
                'diagnostico'   => $validado['diagnostico'] ?? null,
            ]);

            $habitacion->update(['estado' => 'Ocupada']);

            return $registro;
        });

        return response()->json([
            'message' => 'Ingreso registrado correctamente.',
            'data' => $ocupacion->load(['paciente.usuario', 'habitacion']),
        ], 201);
    }

    /**
     * POST /api/hospitalizaciones/{ocupacion}/alta
     * 3) Módulo de alta/salida  4) Registra fecha/hora de salida.
     */
    public function darDeAlta(Request $request, OcupacionHabitacion $ocupacion)
    {
        if ($ocupacion->estado !== 'Activa') {
            return response()->json(['message' => 'Esta estancia ya fue cerrada.'], 422);
        }

        $validado = Validator::make($request->all(), [
            'fecha_salida' => 'required|date|after_or_equal:' . optional($ocupacion->fecha_ingreso)->toDateTimeString(),
            'notas_alta'   => 'nullable|string',
        ])->validate();

        DB::transaction(function () use ($ocupacion, $validado) {
            $ocupacion->update([
                'estado'       => 'Alta',
                'fecha_salida' => $validado['fecha_salida'],
                'notas_alta'   => $validado['notas_alta'] ?? null,
            ]);

            // La habitación pasa a limpieza; alguien debe confirmar que ya
            // está lista (manual, abajo) o el comando programado la libera
            // sola después de un tiempo (ver habitaciones:liberar-limpieza).
            if ($ocupacion->habitacion) {
                $ocupacion->habitacion->update(['estado' => 'Limpieza']);
            }
        });

        return response()->json([
            'message' => 'Alta registrada. La habitación quedó marcada para limpieza.',
            'data' => $ocupacion->fresh(['paciente.usuario', 'habitacion']),
        ]);
    }

    /**
     * GET /api/hospitalizaciones/paciente/{paciente}/historial
     * 5) Historial de estancias del paciente.
     */
    public function historialPaciente(Paciente $paciente)
    {
        $historial = OcupacionHabitacion::with('habitacion')
            ->where('paciente_id', $paciente->id)
            ->orderByDesc('fecha_ingreso')
            ->get();

        return response()->json(['data' => $historial]);
    }

    /**
     * GET /api/hospitalizaciones/reporte?desde=YYYY-MM-DD&hasta=YYYY-MM-DD
     * 6) Reporte de ingresados y dados de alta en un rango de fechas.
     */
    public function reporte(Request $request)
    {
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : now()->startOfMonth();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : now()->endOfDay();

        $ingresosEnRango = OcupacionHabitacion::whereBetween('fecha_ingreso', [$desde, $hasta]);
        $altasEnRango = OcupacionHabitacion::where('estado', 'Alta')
            ->whereBetween('fecha_salida', [$desde, $hasta]);

        $promedioDias = (clone $altasEnRango)
            ->selectRaw('AVG(DATEDIFF(fecha_salida, fecha_ingreso)) as promedio')
            ->value('promedio');

        $porTipoHabitacion = (clone $ingresosEnRango)
            ->join('habitaciones', 'habitaciones.id', '=', 'ocupacion_habitaciones.habitacion_id')
            ->selectRaw('habitaciones.tipo as tipo, COUNT(*) as total')
            ->groupBy('habitaciones.tipo')
            ->get();

        $detalle = (clone $ingresosEnRango)
            ->with(['paciente.usuario', 'habitacion'])
            ->orderByDesc('fecha_ingreso')
            ->get();

        return response()->json([
            'data' => [
                'rango' => [
                    'desde' => $desde->toDateString(),
                    'hasta' => $hasta->toDateString(),
                ],
                'total_ingresos'             => (clone $ingresosEnRango)->count(),
                'total_altas'                => (clone $altasEnRango)->count(),
                'hospitalizados_actualmente' => OcupacionHabitacion::activas()->count(),
                'promedio_dias_estancia'     => $promedioDias ? round((float) $promedioDias, 1) : 0,
                'por_tipo_habitacion'        => $porTipoHabitacion,
                'detalle'                    => $detalle,
            ],
        ]);
    }

    /**
     * PATCH /api/habitaciones/{habitacion}/estado
     * Control manual: el personal marca una habitación como lista
     * (Limpieza -> Disponible) o la manda a mantenimiento y de vuelta.
     * No permite pasar a "Ocupada" a mano: eso solo lo hace el ingreso.
     */
    public function actualizarEstadoHabitacion(Request $request, Habitacion $habitacion)
    {
        $validado = Validator::make($request->all(), [
            'estado' => 'required|in:Disponible,Mantenimiento,Limpieza',
        ])->validate();

        if ($habitacion->estado === 'Ocupada') {
            return response()->json([
                'message' => 'Esta habitación está ocupada; primero da de alta al paciente.',
            ], 422);
        }

        $habitacion->update(['estado' => $validado['estado']]);

        return response()->json([
            'message' => "Habitación marcada como {$validado['estado']}.",
            'data' => $habitacion,
        ]);
    }

    /**
     * GET /api/habitaciones/disponibles
     * Atajo opcional: filtra en el servidor en vez de traer todas y filtrar
     * en el front. El JSX de este módulo puede usar este endpoint o el
     * genérico /api/habitaciones que ya tienes.
     */
    public function habitacionesDisponibles()
    {
        return response()->json([
            'data' => Habitacion::where('estado', 'Disponible')->orderBy('numero')->get(),
        ]);
    }
}