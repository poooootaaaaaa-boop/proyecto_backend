<?php

namespace App\Http\Controllers;
use App\Models\Consulta;
use Illuminate\Http\Request;
use App\Models\AltaPaciente;
use Illuminate\Support\Facades\DB;
use App\Models\Receta;
use App\Models\RecetaDetalle;

class ConsultaController extends Controller
{
    public function addConsulta(Request $request)
{
    $validated = $request->validate([
        'paciente_id' => 'nullable|exists:pacientes,id',
        'cita_id' => 'nullable|exists:citas,id',
        'doctor_id' => 'nullable|exists:doctores,id',
        'motivo' => 'required|string',
        'sintomas' => 'nullable|string',
        'examen' => 'nullable|string',
        'notas_clinicas' => 'nullable|string',
        'fecha_tratamiento' => 'nullable|date',
    ]);

    // Asignar valores por defecto = 1
    $pacienteId = $validated['paciente_id'] ?? 1;
    $citaId = $validated['cita_id'] ?? 1;
    $doctorId = $validated['doctor_id'] ?? 1;

    $paciente = AltaPaciente::find($pacienteId);
    if (!$paciente) {
        return response()->json(['error' => 'Paciente no encontrado'], 404);
    }

    $consulta = Consulta::create([
        'paciente_id' => $validated['paciente_id'],
        'cita_id' => $validated['cita_id'],
        'doctor_id' => $validated['doctor_id'] ?? null,
        'motivo' => $validated['motivo'],
        'sintomas' => $validated['sintomas'] ?? null,
        'examen' => $validated['examen'] ?? null,
        'notas_clinicas' => $validated['notas_clinicas'] ?? null,
        'fecha_tratamiento' => $validated['fecha_tratamiento'] ?? null,
    ]);

    return response()->json([
        'message' => 'Consulta guardada correctamente',
        'consulta' => $consulta,
    ], 201);
}

public function getApiConsulta() {

    $consulta = Consulta::with([
        'paciente.usuario',
        'cita',
        'doctor.usuario',
        'receta.detalles.medicamento' // LA MAGIA
    ])->get();

    return response()->json($consulta);
}


public function getByPaciente($pacienteId)
{
    $consultas = Consulta::where('paciente_id', $pacienteId)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($consultas);
}

public function finalizarConsulta(Request $request)
{
    DB::beginTransaction();

    try {

        // 1. CREAR CONSULTA
        $consulta = Consulta::create([
            'cita_id' => $request->cita_id,
            'doctor_id' => $request->doctor_id,
            'paciente_id' => $request->paciente_id,
            'motivo' => $request->motivo,
            'sintomas' => $request->sintomas,
            'diagnostico' => $request->diagnostico,
            'notas_clinicas' => $request->notas,
            'examen' => $request->examen,
            'fecha_tratamiento' => $request->fecha_tratamiento
        ]);

        // 2. CREAR RECETA
        $receta = Receta::create([
            'consulta_id' => $consulta->id,
            'doctor_id' => $request->doctor_id,
            'paciente_id' => $request->paciente_id,
            'farmacia_id' => $request->farmacia_id, // opcional
            'estado' => 'pendiente',
            'creado_en' => now()
        ]);

        // 3. DETALLE RECETA
        foreach ($request->medicamentos as $med) {
            RecetaDetalle::create([
                'receta_id' => $receta->id,
                'medicamento_id' => $med['medicamento_id'],
                'dosis' => $med['dosis'],
                'frecuencia' => $med['frecuencia'],
                'duracion' => $med['duracion'],
                'instrucciones' => $med['instrucciones'],
            ]);
        }

        DB::commit();

        return response()->json([
            "ok" => true,
            "mensaje" => "Consulta y receta creadas",
            "consulta" => $consulta,
            "receta" => $receta
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            "error" => $e->getMessage()
        ], 500);
    }
}

public function tratamientosLargos($doctor_id)
{
    $hoy = now();

    $tratamientos = \App\Models\Cita::with(['paciente.usuario'])
        ->where('doctor_id', $doctor_id)
        ->where(function ($query) {
            $query->where('motivo', 'like', '%SEGUIMIENTO%')
                  ->orWhere('motivo', 'like', '%RUTINA%');
        })
        ->get();

    return response()->json($tratamientos);
}

public function ultimaConsultaConReceta($paciente_id)
{
    $consulta = Consulta::with([
        'receta.detalles.medicamento'
    ])
    ->where('paciente_id', $paciente_id)
    ->orderBy('id', 'desc') // la más reciente
    ->first();

    return response()->json($consulta);
}


public function recetasPorPaciente($pacienteId)
{
    $recetas = Receta::with([
        'doctor.usuario',
        'doctor.especialidad',
        'paciente.usuario',
        'detalles.medicamento',
        'detalles.medicamento.inventario' 
    ])
    ->where('paciente_id', $pacienteId)
    ->orderBy('creado_en', 'desc')
    ->get();

    return response()->json($recetas);
}
}
