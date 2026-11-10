<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\OcupacionHabitacion;
use App\Models\Paciente;
use App\Models\Consentimiento; 
use App\Models\TrasladoHabitacion;
use App\Models\Receta;
use App\Models\RecetaDetalle;
use App\Models\Cita;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
                'dias_estimados' => $validado['dias_estimados'] ?? null,
            ]);

            $habitacion->update(['estado' => 'Ocupada']);

            $this->guardarDocumentoHospitalizacion(
                'Ingreso',
                'Ingreso hospitalario - ' . $registro->id,
                $registro->id,
                $habitacion->id,
                $registro->paciente_id,
                Paciente::find($registro->paciente_id)?->doctor_id,
                [
                    'Fecha y hora de ingreso' => $validado['fecha_ingreso'],
                    'Motivo' => $validado['motivo'] ?? '—',
                    'Diagnóstico de ingreso' => $validado['diagnostico'] ?? '—',
                    'Estancia estimada' => !empty($validado['dias_estimados']) ? $validado['dias_estimados'] . ' días' : '—',
                ]
            );

            return $registro;
        });

        return response()->json([
            'message' => 'Ingreso registrado correctamente.',
            'data' => $ocupacion->load(['paciente.usuario', 'habitacion']),
            'pdf_url' => url('/api/hospitalizacion-documentos/' . DB::table('hospitalizacion_documentos')->where('ocupacion_id', $ocupacion->id)->where('tipo', 'Ingreso')->latest('id')->value('id') . '/pdf'),
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
        'medicamentos' => 'nullable|array',
        'medicamentos.*.medicamento_id' => 'required|exists:medicamentos,id',
        'medicamentos.*.dosis' => 'nullable|string|max:255',
        'medicamentos.*.frecuencia' => 'nullable|string|max:255',
        'medicamentos.*.duracion' => 'nullable|string|max:255',
        'medicamentos.*.instrucciones' => 'nullable|string',
        'cita_seguimiento' => 'nullable|array',
        'cita_seguimiento.fecha_inicio' => 'required_with:cita_seguimiento|date|after:fecha_salida',
        'cita_seguimiento.fecha_fin' => 'required_with:cita_seguimiento|date|after:cita_seguimiento.fecha_inicio',
    ])->validate();

    $resultado = DB::transaction(function () use ($ocupacion, $validado) {
        $ocupacion->update([
            'estado'              => 'Alta',
            'fecha_alta_medica'   => $validado['fecha_alta_medica'],
            'fecha_salida'        => $validado['fecha_salida'],
            'doctor_alta_id'      => $validado['doctor_alta_id'],
            'diagnostico_egreso'  => $validado['diagnostico_egreso'],
            'tratamiento_egreso'  => $validado['tratamiento_egreso'] ?? null,
            'medicamentos_egreso' => $validado['medicamentos_egreso'] ?? null,
            'recomendaciones'     => $validado['recomendaciones'] ?? null,
            'condicion_egreso'    => $validado['condicion_egreso'],
            'notas_alta'          => $validado['notas_alta'] ?? null,
            'proxima_consulta'    => $validado['cita_seguimiento']['fecha_inicio'] ?? $validado['proxima_consulta'] ?? null,
        ]);

        $habitacionId = $ocupacion->habitacion_id;
        if ($habitacionId) {
            Habitacion::whereKey($habitacionId)->update(['estado' => 'Limpieza']);
        }

        $receta = null;
        $medicamentos = $validado['medicamentos'] ?? [];
        if ($medicamentos) {
            $receta = Receta::create([
                'consulta_id' => null,
                'doctor_id' => $validado['doctor_alta_id'],
                'paciente_id' => $ocupacion->paciente_id,
                'estado' => 'pendiente',
                'creado_en' => now(),
            ]);

            foreach ($medicamentos as $medicamento) {
                RecetaDetalle::create([
                    'receta_id' => $receta->id,
                    'medicamento_id' => $medicamento['medicamento_id'],
                    'dosis' => $medicamento['dosis'] ?? null,
                    'frecuencia' => $medicamento['frecuencia'] ?? null,
                    'duracion' => $medicamento['duracion'] ?? null,
                    'instrucciones' => $medicamento['instrucciones'] ?? null,
                    'creado_en' => now(),
                ]);
            }

            $ocupacion->update(['receta_id' => $receta->id]);
        }

        $cita = null;
        if (!empty($validado['cita_seguimiento'])) {
            $doctor = \App\Models\Doctor::findOrFail($validado['doctor_alta_id']);
            $cita = Cita::create([
                'doctor_id' => $doctor->id,
                'paciente_id' => $ocupacion->paciente_id,
                'clinica_id' => $doctor->clinica_id,
                'fecha_inicio' => $validado['cita_seguimiento']['fecha_inicio'],
                'fecha_fin' => $validado['cita_seguimiento']['fecha_fin'],
                'estado' => 'pendiente',
                'motivo' => 'SEGUIMIENTO DE HOSPITALIZACIÓN - ' . $validado['diagnostico_egreso'],
                'creado_en' => now(),
            ]);
        }

        $this->guardarDocumentoHospitalizacion(
            'Alta',
            'Alta hospitalaria - ' . $ocupacion->id,
            $ocupacion->id,
            $habitacionId,
            $ocupacion->paciente_id,
            $validado['doctor_alta_id'],
            [
                'Fecha y hora de alta médica' => $validado['fecha_alta_medica'],
                'Fecha y hora de salida' => $validado['fecha_salida'],
                'Diagnóstico de egreso' => $validado['diagnostico_egreso'],
                'Condición de egreso' => $validado['condicion_egreso'],
                'Tratamiento' => $validado['tratamiento_egreso'] ?? '—',
                'Medicamentos' => $validado['medicamentos_egreso'] ?? '—',
                'Recomendaciones' => $validado['recomendaciones'] ?? '—',
                'Notas' => $validado['notas_alta'] ?? '—',
                'Receta emitida' => $receta ? 'Sí, receta #' . $receta->id : 'No',
                'Cita de seguimiento' => $cita ? $cita->fecha_inicio : 'No programada',
            ]
        );

        if ($habitacionId) {
            $habitacion = Habitacion::find($habitacionId);
            $this->guardarDocumentoHospitalizacion(
                'Limpieza',
                'Solicitud de limpieza - Habitación ' . ($habitacion->numero ?? $habitacionId),
                $ocupacion->id,
                $habitacionId,
                $ocupacion->paciente_id,
                $validado['doctor_alta_id'],
                [
                    'Habitación' => $habitacion->numero ?? $habitacionId,
                    'Estado' => 'Limpieza',
                    'Fecha de solicitud' => now()->format('Y-m-d H:i:s'),
                    'Motivo' => 'Limpieza requerida después del egreso del paciente.',
                ]
            );
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

        return ['receta' => $receta, 'cita' => $cita];
    });

    try {
        app(EstanciaController::class)->generarReporteFinal($ocupacion->fresh());
    } catch (\Throwable $error) {
        report($error);
    }

    return response()->json([
        'message' => 'Alta registrada. La habitación quedó marcada para limpieza.',
        'data' => $ocupacion->fresh(['paciente.usuario', 'habitacion', 'doctorAlta.usuario']),
        'receta' => $resultado['receta'],
        'cita_seguimiento' => $resultado['cita'],
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

        $estadoAnterior = $habitacion->estado;
        $habitacion->update(['estado' => $validado['estado']]);

        if ($validado['estado'] === 'Limpieza' && $estadoAnterior !== 'Limpieza') {
            $this->guardarDocumentoHospitalizacion(
                'Limpieza',
                'Solicitud de limpieza - Habitación ' . $habitacion->numero,
                null,
                $habitacion->id,
                null,
                null,
                [
                    'Habitación' => $habitacion->numero,
                    'Estado' => 'Limpieza',
                    'Fecha de solicitud' => now()->format('Y-m-d H:i:s'),
                    'Motivo' => 'Limpieza solicitada por el personal hospitalario.',
                ]
            );
        }

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
        'doctor_id'             => 'required|exists:doctores,id',
        'motivo'                => 'nullable|string',
        'fecha_traslado'        => 'nullable|date',
        'familiar_nombre' => 'nullable|string|max:200',
        'firmas.Doctor' => 'required|string',
        'firmas.Familiar' => 'nullable|string',
        'firmas.Paciente' => 'nullable|string',
        'firmas.Clinica' => 'required|string',
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

        $doctor = \App\Models\Doctor::with(['usuario', 'clinica'])->findOrFail($validado['doctor_id']);
        $paciente = Paciente::with('usuario')->findOrFail($ocupacion->paciente_id);
        $nombresFirmantes = [
            'Doctor' => $doctor->usuario->nombre ?? 'Doctor',
            'Clinica' => $doctor->clinica->nombre ?? 'Clínica',
        ];
        if (!empty($validado['firmas']['Familiar'])) {
            $nombresFirmantes['Familiar'] = $validado['familiar_nombre'] ?: 'Familiar';
        }
        if (!empty($validado['firmas']['Paciente'])) {
            $nombresFirmantes['Paciente'] = $paciente->usuario->nombre ?? 'Paciente';
        }
        $firmasPdf = [];
        foreach ($nombresFirmantes as $tipo => $nombre) {
            if (empty($validado['firmas'][$tipo])) {
                continue;
            }
            [$binario, $mime] = $this->decodificarFirma($validado['firmas'][$tipo]);
            DB::table('traslado_firmas')->insert([
                'traslado_id' => $registro->id,
                'tipo' => $tipo,
                'nombre' => $nombre,
                'firma' => $binario,
                'mime' => $mime,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $firmasPdf[$tipo] = 'data:' . $mime . ';base64,' . base64_encode($binario);
        }

        $this->guardarDocumentoHospitalizacion(
            'Traslado',
            'Traslado hospitalario - ' . $registro->id,
            $ocupacion->id,
            $destino->id,
            $ocupacion->paciente_id,
            $doctor->id,
            [
                'Habitación de origen' => $origenId ? 'Hab. ' . (Habitacion::find($origenId)->numero ?? $origenId) : '—',
                'Habitación destino' => 'Hab. ' . $destino->numero,
                'Fecha y hora' => $validado['fecha_traslado'] ?? now()->format('Y-m-d H:i:s'),
                'Motivo' => $validado['motivo'] ?? '—',
                'Nombre del familiar' => $validado['familiar_nombre'] ?? '—',
            ],
            $firmasPdf
        );

        if ($origenId) {
            $origen = Habitacion::find($origenId);
            $this->guardarDocumentoHospitalizacion(
                'Limpieza',
                'Solicitud de limpieza - Habitación ' . ($origen->numero ?? $origenId),
                $ocupacion->id,
                $origenId,
                $ocupacion->paciente_id,
                $doctor->id,
                [
                    'Habitación' => $origen->numero ?? $origenId,
                    'Estado' => 'Limpieza',
                    'Fecha de solicitud' => now()->format('Y-m-d H:i:s'),
                    'Motivo' => 'Limpieza requerida después del traslado del paciente.',
                ]
            );
        }

        return $registro;
    });

    return response()->json([
        'message' => 'Traslado registrado correctamente.',
        'data' => $traslado->load(['habitacionOrigen', 'habitacionDestino', 'doctor.usuario']),
        'pdf_url' => url('/api/hospitalizacion-documentos/' . DB::table('hospitalizacion_documentos')->where('tipo', 'Traslado')->where('ocupacion_id', $ocupacion->id)->latest('id')->value('id') . '/pdf'),
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

public function documentos(Request $request)
{
    $query = DB::table('hospitalizacion_documentos as hd')
        ->leftJoin('pacientes as p', 'p.id', '=', 'hd.paciente_id')
        ->leftJoin('usuarios as up', 'up.id', '=', 'p.usuario_id')
        ->leftJoin('doctores as d', 'd.id', '=', 'hd.doctor_id')
        ->leftJoin('usuarios as ud', 'ud.id', '=', 'd.usuario_id')
        ->when($request->filled('tipo_documento'), fn ($q) => $q->where('hd.tipo', $request->tipo_documento))
        ->when($request->filled('desde'), fn ($q) => $q->whereDate('hd.created_at', '>=', $request->desde))
        ->when($request->filled('hasta'), fn ($q) => $q->whereDate('hd.created_at', '<=', $request->hasta));

    if ($request->filled('buscar')) {
        $buscar = $request->buscar;
        $query->where(function ($q) use ($buscar) {
            $q->where('up.nombre', 'like', "%{$buscar}%");
        });
    }

    if ($request->filled('nombre')) {
        $nombre = $request->nombre;
        $query->where('hd.nombre', 'like', "%{$nombre}%");
    }

    $documentos = $query->orderByDesc('hd.created_at')
        ->select('hd.id', 'hd.tipo', 'hd.nombre', 'hd.paciente_id', 'hd.doctor_id', 'hd.created_at', 'up.nombre as paciente_nombre', 'ud.nombre as doctor_nombre')
        ->get()
        ->map(fn ($documento) => [
            'id' => $documento->id,
            'tipo_documento' => $documento->tipo,
            'nombre_documento' => $documento->nombre,
            'paciente' => $documento->paciente_id ? [
                'id' => $documento->paciente_id,
                'usuario' => ['nombre' => $documento->paciente_nombre],
            ] : null,
            'doctor' => $documento->doctor_id ? [
                'id' => $documento->doctor_id,
                'usuario' => ['nombre' => $documento->doctor_nombre],
            ] : null,
            'formato' => null,
            'created_at' => $documento->created_at,
            'estado' => 'Finalizado',
            'pdf_url' => url('/api/hospitalizacion-documentos/' . $documento->id . '/pdf'),
            'download_url' => url('/api/hospitalizacion-documentos/' . $documento->id . '/pdf?download=1'),
        ]);

    return response()->json(['data' => $documentos]);
}

public function descargarDocumento(Request $request, int $documento)
{
    $registro = DB::table('hospitalizacion_documentos')->where('id', $documento)->first();
    abort_if(!$registro, 404, 'Documento hospitalario no encontrado.');

    $disposicion = $request->boolean('download') ? 'attachment' : 'inline';
    $nombre = Str::slug($registro->nombre) . '.pdf';

    return response($registro->contenido, 200, [
        'Content-Type' => $registro->mime ?: 'application/pdf',
        'Content-Disposition' => $disposicion . '; filename="' . $nombre . '"',
    ]);
}

private function guardarDocumentoHospitalizacion(
    string $tipo,
    string $nombre,
    ?int $ocupacionId,
    ?int $habitacionId,
    ?int $pacienteId,
    ?int $doctorId,
    array $datos,
    array $firmas = []
): int {
    $ocupacion = $ocupacionId
        ? OcupacionHabitacion::with(['paciente.usuario'])->find($ocupacionId)
        : null;
    $paciente = $pacienteId ? Paciente::with('usuario')->find($pacienteId) : $ocupacion?->paciente;
    $doctor = $doctorId ? \App\Models\Doctor::with(['usuario', 'clinica'])->find($doctorId) : null;
    $habitacion = $habitacionId ? Habitacion::find($habitacionId) : null;

    $pdf = Pdf::loadView('pdf.hospitalizacion', [
        'tipo' => $tipo,
        'nombre' => $nombre,
        'documentoId' => $ocupacionId,
        'datos' => $datos,
        'paciente' => $paciente,
        'doctor' => $doctor,
        'clinica' => $doctor?->clinica,
        'habitacion' => $habitacion,
        'firmas' => $firmas,
    ])->setPaper('letter');

    $contenido = $pdf->output();

    return (int) DB::table('hospitalizacion_documentos')->insertGetId([
        'ocupacion_id' => $ocupacionId,
        'habitacion_id' => $habitacionId,
        'paciente_id' => $paciente?->id ?? $pacienteId,
        'doctor_id' => $doctorId,
        'tipo' => $tipo,
        'nombre' => $nombre,
        'contenido' => $contenido,
        'mime' => 'application/pdf',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

private function decodificarFirma(string $dataUrl): array
{
    if (!preg_match('/^data:(image\/(?:png|jpeg));base64,([A-Za-z0-9+\/=]+)$/', $dataUrl, $matches)) {
        abort(422, 'El formato de una de las firmas no es válido.');
    }

    $binario = base64_decode($matches[2], true);
    if ($binario === false || strlen($binario) > 2 * 1024 * 1024) {
        abort(422, 'Una firma está vacía o supera el tamaño permitido.');
    }

    return [$binario, $matches[1]];
}
}