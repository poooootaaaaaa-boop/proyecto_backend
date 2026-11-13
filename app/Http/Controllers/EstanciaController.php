<?php

namespace App\Http\Controllers;

use App\Models\EstanciaConsumo;
use App\Models\EstanciaRegistro;
use App\Models\InsumoEstancia;
use App\Models\OcupacionHabitacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstanciaController extends Controller
{
    public function activasDoctor(Request $request, int $doctor)
    {
        $estado = $request->input('estado', 'Activa');
        abort_unless(in_array($estado, ['Activa', 'Alta'], true), 422, 'Estado de estancia no válido.');

        // Estancias donde el doctor es el asignado al paciente O está vinculado
        // por el consentimiento (responsable o vigilancia).
        $estancias = OcupacionHabitacion::where('estado', $estado)
            ->where(function ($query) use ($doctor) {
                $query->whereHas('paciente', fn ($q) => $q->where('doctor_id', $doctor))
                    ->orWhereIn('id', function ($sub) use ($doctor) {
                        $sub->select('ch.ocupacion_id')
                            ->from('consentimiento_hospitalizacion as ch')
                            ->join('consentimientos as c', 'c.id', '=', 'ch.consentimiento_id')
                            ->whereNotNull('ch.ocupacion_id')
                            ->where(function ($w) use ($doctor) {
                                $w->where('c.doctor_id', $doctor)
                                    ->orWhereExists(function ($e) use ($doctor) {
                                        $e->select(DB::raw(1))
                                            ->from('consentimiento_vigilancia as cv')
                                            ->whereColumn('cv.consentimiento_id', 'c.id')
                                            ->where('cv.doctor_id', $doctor);
                                    });
                            });
                    });
            })
            ->with(['paciente.usuario', 'habitacion'])
            ->orderByDesc('fecha_ingreso')
            ->get();

        $data = $estancias->map(function ($estancia) {
            $ultimoRegistro = EstanciaRegistro::where('ocupacion_id', $estancia->id)
                ->where('tipo', 'evolucion')
                ->latest('fecha_hora')
                ->value('descripcion');

            return [
                'id' => $estancia->id,
                'paciente_id' => $estancia->paciente_id,
                'paciente' => $estancia->paciente,
                'habitacion' => $estancia->habitacion,
                'fecha_ingreso' => $estancia->fecha_ingreso,
                'dias_estancia' => $estancia->fecha_ingreso ? now()->startOfDay()->diffInDays($estancia->fecha_ingreso->copy()->startOfDay()) + 1 : null,
                'diagnostico' => $estancia->diagnostico,
                'estado' => $estancia->estado,
                'estado_actual' => $ultimoRegistro ?: ($estancia->diagnostico ?: 'Sin evolución registrada'),
                'costo_total' => (float) EstanciaConsumo::where('ocupacion_id', $estancia->id)->sum('subtotal'),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function detalle(Request $request, OcupacionHabitacion $ocupacion)
    {
        $doctorId = $request->validate(['doctor_id' => 'required|exists:doctores,id'])['doctor_id'];
        $this->validarDoctorAsignado($ocupacion, (int) $doctorId, false);

        return response()->json(['data' => $this->datosEstancia($ocupacion)]);
    }

    public function evolucion(Request $request, OcupacionHabitacion $ocupacion)
    {
        return $this->guardarRegistro($request, $ocupacion, 'evolucion');
    }

    public function procedimiento(Request $request, OcupacionHabitacion $ocupacion)
    {
        return $this->guardarRegistro($request, $ocupacion, 'procedimiento');
    }

    public function observacion(Request $request, OcupacionHabitacion $ocupacion)
    {
        return $this->guardarRegistro($request, $ocupacion, 'observacion');
    }

    private function guardarRegistro(Request $request, OcupacionHabitacion $ocupacion, string $tipo)
    {
        $datos = $request->validate([
            'doctor_id' => 'required|exists:doctores,id',
            'descripcion' => 'required|string|min:2|max:10000',
            'costo' => $tipo === 'procedimiento' ? 'nullable|numeric|min:0|max:9999999999.99' : 'prohibited',
            'fecha_hora' => 'nullable|date',
        ]);
        $this->validarDoctorAsignado($ocupacion, (int) $datos['doctor_id'], true);

        $registro = EstanciaRegistro::create([
            'ocupacion_id' => $ocupacion->id,
            'paciente_id' => $ocupacion->paciente_id,
            'doctor_id' => $datos['doctor_id'],
            'tipo' => $tipo,
            'descripcion' => trim($datos['descripcion']),
            'costo' => $tipo === 'procedimiento' ? ($datos['costo'] ?? 0) : null,
            'fecha_hora' => $datos['fecha_hora'] ?? now(),
        ]);

        return response()->json(['data' => $registro->load('doctor.usuario')], 201);
    }

    public function consumo(Request $request, OcupacionHabitacion $ocupacion)
    {
        $datos = $request->validate([
            'doctor_id' => 'required|exists:doctores,id',
            'insumo_id' => 'required|exists:insumos_estancia,id',
            'cantidad' => 'required|numeric|gt:0|max:999999999',
            'observaciones' => 'nullable|string|max:5000',
            'fecha_hora' => 'nullable|date',
        ]);
        $this->validarDoctorAsignado($ocupacion, (int) $datos['doctor_id'], true);

        $insumo = InsumoEstancia::whereKey($datos['insumo_id'])->where('activo', true)->first();
        if (!$insumo) {
            return response()->json(['message' => 'El insumo no existe o está inactivo.'], 422);
        }

        $cantidad = round((float) $datos['cantidad'], 3);
        $precio = (float) $insumo->precio_unitario;
        $consumo = EstanciaConsumo::create([
            'ocupacion_id' => $ocupacion->id,
            'paciente_id' => $ocupacion->paciente_id,
            'doctor_id' => $datos['doctor_id'],
            'insumo_id' => $insumo->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => round($cantidad * $precio, 2),
            'observaciones' => $datos['observaciones'] ?? null,
            'fecha_hora' => $datos['fecha_hora'] ?? now(),
        ]);

        return response()->json(['data' => $consumo->load(['insumo', 'doctor.usuario'])], 201);
    }

    public function historialPaciente(int $paciente)
    {
        $estancias = OcupacionHabitacion::where('paciente_id', $paciente)
            ->with(['habitacion', 'registrosEstancia.doctor.usuario', 'consumosEstancia.insumo', 'consumosEstancia.doctor.usuario'])
            ->orderByDesc('fecha_ingreso')
            ->get()
            ->map(fn ($estancia) => $this->datosEstancia($estancia));

        return response()->json(['data' => $estancias]);
    }

    public function reporteFinal(OcupacionHabitacion $ocupacion)
    {
        if ($ocupacion->estado !== 'Alta') {
            return response()->json(['message' => 'El reporte final estará disponible cuando termine la estancia.'], 422);
        }

        return response()->json(['data' => $this->datosEstancia($ocupacion)]);
    }

    public function pdfFinal(OcupacionHabitacion $ocupacion)
    {
        if ($ocupacion->estado !== 'Alta') {
            return response()->json(['message' => 'El reporte PDF se genera al finalizar la estancia.'], 422);
        }

        $documento = DB::table('hospitalizacion_documentos')
            ->where('ocupacion_id', $ocupacion->id)
            ->where('tipo', 'Reporte estancia')
            ->latest('id')
            ->first();

        if (!$documento) {
            $this->generarReporteFinal($ocupacion);
            $documento = DB::table('hospitalizacion_documentos')
                ->where('ocupacion_id', $ocupacion->id)
                ->where('tipo', 'Reporte estancia')
                ->latest('id')
                ->first();
        }

        return response($documento->contenido, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reporte_estancia_' . $ocupacion->id . '.pdf"',
        ]);
    }

    public function generarReporteFinal(OcupacionHabitacion $ocupacion): void
    {
        if ($ocupacion->estado !== 'Alta') {
            return;
        }

        $data = $this->datosEstancia($ocupacion);
        $pdf = Pdf::loadView('pdf.reporte_estancia', ['estancia' => $data])->setPaper('letter');
        $nombre = 'Reporte final de estancia - ' . $ocupacion->id;

        DB::table('hospitalizacion_documentos')->insert([
            'ocupacion_id' => $ocupacion->id,
            'habitacion_id' => $ocupacion->habitacion_id,
            'paciente_id' => $ocupacion->paciente_id,
            'doctor_id' => $ocupacion->doctor_alta_id,
            'tipo' => 'Reporte estancia',
            'nombre' => $nombre,
            'contenido' => $pdf->output(),
            'mime' => 'application/pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function datosEstancia(OcupacionHabitacion $ocupacion): array
    {
        $ocupacion->loadMissing(['paciente.usuario', 'habitacion', 'doctorAlta.usuario']);
        $registros = EstanciaRegistro::with('doctor.usuario')
            ->where('ocupacion_id', $ocupacion->id)
            ->orderBy('fecha_hora')
            ->get();
        $consumos = EstanciaConsumo::with(['insumo', 'doctor.usuario'])
            ->where('ocupacion_id', $ocupacion->id)
            ->orderBy('fecha_hora')
            ->get();

        $costos = $consumos->groupBy(fn ($consumo) => $consumo->insumo?->categoria ?? 'sin_categoria')
            ->map(fn ($items, $categoria) => [
                'categoria' => $categoria,
                'cantidad_registros' => $items->count(),
                'total' => round((float) $items->sum('subtotal'), 2),
            ])->values();
        $costoProcedimientos = round((float) $registros->where('tipo', 'procedimiento')->sum('costo'), 2);
        $cantidadProcedimientos = $registros->where('tipo', 'procedimiento')->count();
        if ($cantidadProcedimientos > 0) {
            $costos->push([
                'categoria' => 'procedimiento',
                'cantidad_registros' => $cantidadProcedimientos,
                'total' => $costoProcedimientos,
            ]);
        }

        return [
            'id' => $ocupacion->id,
            'paciente_id' => $ocupacion->paciente_id,
            'paciente' => $ocupacion->paciente,
            'habitacion' => $ocupacion->habitacion,
            'doctor_alta' => $ocupacion->doctorAlta,
            'fecha_ingreso' => $ocupacion->fecha_ingreso,
            'fecha_salida' => $ocupacion->fecha_salida,
            'estado' => $ocupacion->estado,
            'diagnostico' => $ocupacion->diagnostico,
            'diagnostico_egreso' => $ocupacion->diagnostico_egreso,
            'condicion_egreso' => $ocupacion->condicion_egreso,
            'registros' => $registros,
            'consumos' => $consumos,
            'costos_por_categoria' => $costos,
            'costo_procedimientos' => $costoProcedimientos,
            'costo_total' => round((float) $consumos->sum('subtotal') + $costoProcedimientos, 2),
        ];
    }

    private function validarDoctorAsignado(OcupacionHabitacion $ocupacion, int $doctorId, bool $requiereActiva): void
    {
        abort_if($requiereActiva && $ocupacion->estado !== 'Activa', 422, 'La estancia ya no está activa.');
        abort_unless($this->doctorVinculado($ocupacion, $doctorId), 403, 'El doctor no está asignado a este paciente.');
    }

    /**
     * Un doctor está vinculado a la estancia si es el doctor asignado al paciente,
     * el responsable del consentimiento de la estancia o uno de sus doctores de vigilancia.
     */
    private function doctorVinculado(OcupacionHabitacion $ocupacion, int $doctorId): bool
    {
        $doctorPaciente = DB::table('pacientes')->where('id', $ocupacion->paciente_id)->value('doctor_id');
        if ($doctorPaciente !== null && (int) $doctorPaciente === $doctorId) {
            return true;
        }

        $consentimientoIds = DB::table('consentimiento_hospitalizacion')
            ->where('ocupacion_id', $ocupacion->id)
            ->pluck('consentimiento_id');

        if ($consentimientoIds->isEmpty()) {
            return false;
        }

        return DB::table('consentimientos')
                ->whereIn('id', $consentimientoIds)
                ->where('doctor_id', $doctorId)
                ->exists()
            || DB::table('consentimiento_vigilancia')
                ->whereIn('consentimiento_id', $consentimientoIds)
                ->where('doctor_id', $doctorId)
                ->exists();
    }
}