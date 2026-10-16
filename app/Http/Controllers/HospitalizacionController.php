<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\OcupacionHabitacion;
use App\Models\Paciente;
use App\Models\Consentimiento; 
use App\Models\TrasladoHabitacion;
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

$ocupaciones = $query->get()->each(function ($o) {
    $o->dias_estancia = $o->fecha_ingreso
        ? (int) Carbon::parse($o->fecha_ingreso)
            ->startOfDay()
            ->diffInDays(now()->startOfDay()) + 1
        : null;
});

    return response()->json(['data' => $ocupaciones]);
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
            'dias_estimados' => 'nullable|integer|min:1|max:365',
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
     * 3) Módulo de alta/salida  4) Registra fecha/hora de salida y todo
     * el checklist clínico de cierre de la estancia.
     */
public function darDeAlta(Request $request, OcupacionHabitacion $ocupacion)
{
    if ($ocupacion->estado !== 'Activa') {
        return response()->json(['message' => 'Esta estancia ya fue cerrada.'], 422);
    }

    $validado = Validator::make($request->all(), [
        'fecha_alta_medica'   => 'required|date',
        'fecha_salida'        => 'required|date|after_or_equal:fecha_alta_medica',
        'doctor_alta_id'      => 'required|exists:doctores,id',
        'diagnostico_egreso'  => 'required|string|max:255',
        'tratamiento_egreso'  => 'nullable|string',
        'medicamentos_egreso' => 'nullable|string',
        'recomendaciones'     => 'nullable|string',
        'proxima_consulta'    => 'nullable|date',
        'condicion_egreso'    => 'required|in:Mejorado,Curado,Referido,Voluntaria,Fallecido',
        'notas_alta'          => 'nullable|string',
    ])->validate();

    DB::transaction(function () use ($ocupacion, $validado) {
        $ocupacion->update([
            'estado'              => 'Alta',
            'fecha_alta_medica'   => $validado['fecha_alta_medica'],
            'fecha_salida'        => $validado['fecha_salida'],
            'doctor_alta_id'      => $validado['doctor_alta_id'],
            'diagnostico_egreso'  => $validado['diagnostico_egreso'],
            'tratamiento_egreso'  => $validado['tratamiento_egreso'] ?? null,
            'medicamentos_egreso' => $validado['medicamentos_egreso'] ?? null,
            'recomendaciones'     => $validado['recomendaciones'] ?? null,
            'proxima_consulta'    => $validado['proxima_consulta'] ?? null,
            'condicion_egreso'    => $validado['condicion_egreso'],
            'notas_alta'          => $validado['notas_alta'] ?? null,
        ]);

        if ($ocupacion->habitacion) {
            $ocupacion->habitacion->update(['estado' => 'Limpieza']);
        }

        // ---- NUEVO: finalizar el/los consentimiento(s) ligados a esta hospitalización ----
        $consentimientoIds = DB::table('consentimiento_hospitalizacion')
            ->where('ocupacion_id', $ocupacion->id)
            ->pluck('consentimiento_id');

        if ($consentimientoIds->isNotEmpty()) {
            $now = now();

            Consentimiento::whereIn('id', $consentimientoIds)
                ->where('estado', '!=', 'Cancelado')
                ->update([
                    'estado'           => 'Finalizado',
                    'fecha_finalizado' => $now,
                ]);

            $usuarioIdDoctorAlta = DB::table('doctores')
                ->where('id', $validado['doctor_alta_id'])
                ->value('usuario_id');

            foreach ($consentimientoIds as $consentimientoId) {
                DB::table('consentimiento_historial')->insert([
                    'consentimiento_id' => $consentimientoId,
                    'usuario_id'        => $usuarioIdDoctorAlta,
                    'accion'            => 'Finalizado automáticamente',
                    'descripcion'       => 'El consentimiento se finalizó automáticamente al dar de alta al paciente.',
                    'created_at'        => $now,
                ]);
            }
        }
    });

    return response()->json([
        'message' => 'Alta registrada. La habitación quedó marcada para limpieza.',
        'data' => $ocupacion->fresh(['paciente.usuario', 'habitacion', 'doctorAlta.usuario']),
    ]);
}

    /**
     * GET /api/hospitalizaciones/paciente/{paciente}/historial
     * 5) Historial de estancias del paciente.
     */
    public function historialPaciente(Paciente $paciente)
    {
        $historial = OcupacionHabitacion::with(['habitacion', 'doctorAlta.usuario'])
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
     * GET /api/consentimientos?buscar=&desde=&hasta=
     * Historial de documentos (consentimientos) para volver a descargarlos.
     * Si ya tienes un ConsentimientoController, mueve este método ahí en vez
     * de dejarlo aquí — funcionalmente es lo mismo.
     */
    public function listarDocumentos(Request $request)
    {
        $query = Consentimiento::with(['paciente.usuario', 'doctor.usuario', 'formato'])
            ->orderByDesc('created_at');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('paciente.usuario', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * POST /api/consentimientos/{consentimiento}/finalizar
     * Marca el documento como Finalizado. Se llama justo cuando el usuario
     * da clic en "Finalizar y descargar" en PruebaConsentimientoFirma, o a
     * mano desde la tabla de Documentos si un consentimiento viejo se quedó
     * pendiente por error.
     * Si ya tienes un ConsentimientoController, mueve este método ahí.
     */
    public function finalizarConsentimiento(Consentimiento $consentimiento)
    {
        if ($consentimiento->estado === 'Cancelado') {
            return response()->json(['message' => 'Este consentimiento está cancelado, no se puede finalizar.'], 422);
        }

        $consentimiento->update([
            'estado'           => 'Finalizado',
            'fecha_finalizado' => now(),
        ]);

        return response()->json([
            'message' => 'Consentimiento marcado como finalizado.',
            'data' => $consentimiento->fresh(),
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

    /**
 * POST /api/hospitalizaciones/{ocupacion}/traslado
 * Mueve al paciente a otra habitación sin cerrar la estancia.
 */
    public function trasladar(Request $request, OcupacionHabitacion $ocupacion)
    {
        if ($ocupacion->estado !== 'Activa') {
        return response()->json(['message' => 'Esta estancia no está activa.'], 422);
    }

    $validado = Validator::make($request->all(), [
        'habitacion_destino_id' => 'required|exists:habitaciones,id',
        'doctor_id'             => 'nullable|exists:doctores,id',
        'motivo'                => 'nullable|string',
        'fecha_traslado'        => 'nullable|date',
    ])->validate();

    if ((int) $validado['habitacion_destino_id'] === (int) $ocupacion->habitacion_id) {
        return response()->json(['message' => 'El paciente ya está en esa habitación.'], 422);
    }

    $destino = Habitacion::findOrFail($validado['habitacion_destino_id']);
    if ($destino->estado !== 'Disponible') {
        return response()->json(['message' => 'La habitación de destino no está disponible.'], 422);
    }

    $traslado = DB::transaction(function () use ($ocupacion, $destino, $validado) {
        $origenId = $ocupacion->habitacion_id;

        $registro = TrasladoHabitacion::create([
            'ocupacion_id'          => $ocupacion->id,
            'habitacion_origen_id'  => $origenId,
            'habitacion_destino_id' => $destino->id,
            'doctor_id'             => $validado['doctor_id'] ?? null,
            'motivo'                => $validado['motivo'] ?? null,
            'fecha_traslado'        => $validado['fecha_traslado'] ?? now(),
        ]);

        $ocupacion->update(['habitacion_id' => $destino->id]);
        $destino->update(['estado' => 'Ocupada']);

        if ($origenId) {
            Habitacion::where('id', $origenId)->update(['estado' => 'Limpieza']);
        }

        return $registro;
    });

    return response()->json([
        'message' => 'Traslado registrado correctamente.',
        'data' => $traslado->load(['habitacionOrigen', 'habitacionDestino', 'doctor.usuario']),
    ], 201);
    }

/**
 * GET /api/hospitalizaciones/{ocupacion}/traslados
 * Historial de traslados de una estancia.
 */
public function historialTraslados(OcupacionHabitacion $ocupacion)
{
    $traslados = TrasladoHabitacion::with(['habitacionOrigen', 'habitacionDestino', 'doctor.usuario'])
        ->where('ocupacion_id', $ocupacion->id)
        ->orderByDesc('fecha_traslado')
        ->get();

    return response()->json(['data' => $traslados]);
}
}