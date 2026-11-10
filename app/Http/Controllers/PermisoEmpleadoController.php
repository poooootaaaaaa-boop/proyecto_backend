<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResuelveSolicitudes;
use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\Empleado;
use App\Models\PermisoEmpleado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CLIC-388 · Registrar permisos */
class PermisoEmpleadoController extends Controller
{
    use ScopesPorClinica, ResuelveSolicitudes;

    private const TIPOS = [
        'personal'              => 'Personal',
        'medico'                => 'Médico / incapacidad',
        'estudio'               => 'Estudio',
        'duelo'                 => 'Duelo',
        'maternidad_paternidad' => 'Maternidad / paternidad',
        'otro'                  => 'Otro',
    ];

    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $query = PermisoEmpleado::with('empleado.puesto:id,nombre')
            ->whereHas('empleado', fn ($q) => $q->where('clinica_id', $clinicaId));

        foreach (['estado', 'empleado_id', 'tipo'] as $filtro) {
            if ($request->filled($filtro)) {
                $query->where($filtro, $request->input($filtro));
            }
        }

        return response()->json(
            $query->orderByDesc('fecha_inicio')->orderByDesc('id')->get()
        );
    }

    public function store(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $data = $request->validate([
            'empleado_id'     => ['required', 'integer',
                Rule::exists('empleados', 'id')->where('clinica_id', $clinicaId)],
            'tipo'            => ['required', Rule::in(array_keys(self::TIPOS))],
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after_or_equal:fecha_inicio',
            'con_goce_sueldo' => 'nullable|boolean',
            'motivo'          => 'nullable|string|max:1000',
        ], $this->mensajes(), $this->atributos());

        $empleado = Empleado::findOrFail($data['empleado_id']);

        if ($empleado->estado !== 'activo') {
            return response()->json(['message' => 'El empleado está dado de baja.'], 422);
        }

        if ($choque = $empleado->ausenciaEnRango($data['fecha_inicio'], $data['fecha_fin'])) {
            return response()->json([
                'message' => "El empleado ya tiene un registro de {$choque} que se traslapa con esas fechas.",
            ], 422);
        }

        $inicio = Carbon::parse($data['fecha_inicio'])->startOfDay();
        $fin    = Carbon::parse($data['fecha_fin'])->startOfDay();

        $permiso = PermisoEmpleado::create(array_merge($data, [
            'dias'            => (int) $inicio->diffInDays($fin) + 1, // días naturales
            'estado'          => 'pendiente',
            'con_goce_sueldo' => $data['con_goce_sueldo'] ?? true,
        ]));

        return response()->json([
            'message' => 'Permiso registrado, queda pendiente de aprobación',
            'permiso' => $permiso->load('empleado.puesto:id,nombre'),
        ], 201);
    }

    /** Body: { clinica_id, estado: aprobado|rechazado|cancelado, observaciones?, registrado_por? } */
    public function resolver(Request $request, PermisoEmpleado $permiso)
    {
        $permiso->loadMissing('empleado');

        $tipo = self::TIPOS[$permiso->tipo] ?? $permiso->tipo;
        $desc = "Permiso ({$tipo}) aprobado del {$permiso->fecha_inicio->format('d/m/Y')} al "
              . "{$permiso->fecha_fin->format('d/m/Y')} ({$permiso->dias} día(s), "
              . ($permiso->con_goce_sueldo ? 'con' : 'sin') . ' goce de sueldo).';

        return $this->resolverSolicitud($request, $permiso, 'permiso', $desc);
    }
}