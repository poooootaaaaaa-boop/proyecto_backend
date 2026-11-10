<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\Empleado;
use App\Models\Puesto;
use App\Models\PermisoEmpleado;
use App\Models\VacacionEmpleado;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** CLIC-385 · Registrar empleados (+ KPIs del módulo) */
class EmpleadoController extends Controller
{
    use ScopesPorClinica;

    /** Tarjetas superiores de la pantalla de Gestión de Personal. */
    public function resumen(Request $request)
    {
        $clinicaId = $this->clinicaId($request);
        $hoy = Carbon::today()->toDateString();

        $delaClinica = fn ($q) => $q->where('clinica_id', $clinicaId);
        $vigente = fn ($q) => $q->where('estado', 'aprobado')
            ->whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy);

        return response()->json([
            'empleados_activos'    => Empleado::where('clinica_id', $clinicaId)->where('estado', 'activo')->count(),
            'bajas'                => Empleado::where('clinica_id', $clinicaId)->where('estado', 'baja')->count(),
            'sin_horario'          => Empleado::where('clinica_id', $clinicaId)->where('estado', 'activo')->doesntHave('horarios')->count(),
            'en_vacaciones_hoy'    => VacacionEmpleado::whereHas('empleado', $delaClinica)->where($vigente)->distinct('empleado_id')->count('empleado_id'),
            'con_permiso_hoy'      => PermisoEmpleado::whereHas('empleado', $delaClinica)->where($vigente)->distinct('empleado_id')->count('empleado_id'),
            'permisos_pendientes'  => PermisoEmpleado::whereHas('empleado', $delaClinica)->where('estado', 'pendiente')->count(),
            'vacaciones_pendientes'=> VacacionEmpleado::whereHas('empleado', $delaClinica)->where('estado', 'pendiente')->count(),
        ]);
    }

    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $query = Empleado::with('puesto:id,nombre')->where('clinica_id', $clinicaId);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('puesto_id')) {
            $query->where('puesto_id', $request->puesto_id);
        }

        if ($request->filled('buscar')) {
            $b = '%' . $request->buscar . '%';
            $query->where(function ($q) use ($b) {
                $q->where('nombre', 'like', $b)
                    ->orWhere('apellidos', 'like', $b)
                    ->orWhere('numero_empleado', 'like', $b)
                    ->orWhere('correo', 'like', $b);
            });
        }

        return response()->json(
            $query->orderBy('apellidos')->orderBy('nombre')->get()
        );
    }

    public function show(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);

        return response()->json($empleado->load(['puesto:id,nombre', 'horarios']));
    }

    public function store(Request $request)
    {
        $clinicaId = $this->clinicaId($request);
        $this->normalizar($request);

        $data = $request->validate($this->reglas($clinicaId), $this->mensajes(), $this->atributos());

        $empleado = DB::transaction(function () use ($data, $clinicaId, $request) {
            $empleado = Empleado::create(array_merge($data, [
                'clinica_id'      => $clinicaId,
                'numero_empleado' => $this->siguienteNumero($clinicaId),
                'estado'          => 'activo',
            ]));

            $puesto = $empleado->puesto?->nombre ?? 'sin puesto';
            $empleado->registrarHistorial(
                'alta',
                "Alta en la clínica como {$puesto}.",
                [
                    'fecha'          => $empleado->fecha_ingreso->toDateString(),
                    'valor_nuevo'    => $puesto,
                    'registrado_por' => $this->registradoPor($request),
                ]
            );

            return $empleado;
        });

        return response()->json([
            'message'  => 'Empleado registrado correctamente',
            'empleado' => $empleado->load('puesto:id,nombre'),
        ], 201);
    }

    public function update(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);
        $this->normalizar($request);

        $data = $request->validate(
            $this->reglas($empleado->clinica_id, $empleado),
            $this->mensajes(),
            $this->atributos()
        );

        DB::transaction(function () use ($empleado, $data, $request) {
            $puestoAnterior  = $empleado->puesto?->nombre;
            $salarioAnterior = $empleado->salario_mensual;
            $puestoIdAntes   = $empleado->puesto_id;

            $empleado->update($data);
            $empleado->load('puesto');

            $reg = $this->registradoPor($request);

            if ((int) $puestoIdAntes !== (int) $empleado->puesto_id) {
                $nuevo = $empleado->puesto?->nombre ?? 'sin puesto';
                $empleado->registrarHistorial(
                    'cambio_puesto',
                    'Cambio de puesto: ' . ($puestoAnterior ?? 'sin puesto') . " → {$nuevo}.",
                    ['valor_anterior' => $puestoAnterior, 'valor_nuevo' => $nuevo, 'registrado_por' => $reg]
                );
            }

            if ((float) $salarioAnterior !== (float) $empleado->salario_mensual) {
                $fmt = fn ($v) => $v === null ? 'sin definir' : '$' . number_format((float) $v, 2);
                $empleado->registrarHistorial(
                    'cambio_salario',
                    'Cambio de salario mensual: ' . $fmt($salarioAnterior) . ' → ' . $fmt($empleado->salario_mensual) . '.',
                    [
                        'valor_anterior' => $fmt($salarioAnterior),
                        'valor_nuevo'    => $fmt($empleado->salario_mensual),
                        'registrado_por' => $reg,
                    ]
                );
            }
        });

        return response()->json([
            'message'  => 'Empleado actualizado',
            'empleado' => $empleado->fresh('puesto:id,nombre'),
        ]);
    }

    /** Dar de baja (no se borra: se conserva el historial). */
    public function baja(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);

        if ($empleado->estado === 'baja') {
            return response()->json(['message' => 'El empleado ya está dado de baja.'], 422);
        }

        $data = $request->validate([
            'fecha_baja'  => 'required|date',
            'motivo_baja' => 'required|string|max:255',
        ], $this->mensajes(), $this->atributos());

        if (Carbon::parse($data['fecha_baja'])->lt($empleado->fecha_ingreso)) {
            return response()->json(['message' => 'La fecha de baja no puede ser anterior a la fecha de ingreso.'], 422);
        }

        DB::transaction(function () use ($empleado, $data, $request) {
            $empleado->update(array_merge($data, ['estado' => 'baja']));

            // Lo pendiente deja de tener sentido
            $cancelar = ['estado' => 'cancelado', 'observaciones' => 'Cancelado por baja del empleado', 'resuelto_en' => now()];
            $empleado->permisos()->where('estado', 'pendiente')->update($cancelar);
            $empleado->vacaciones()->where('estado', 'pendiente')->update($cancelar);

            $empleado->registrarHistorial('baja', 'Baja: ' . $data['motivo_baja'], [
                'fecha'          => $data['fecha_baja'],
                'valor_nuevo'    => $data['motivo_baja'],
                'registrado_por' => $this->registradoPor($request),
            ]);
        });

        return response()->json(['message' => 'Empleado dado de baja', 'empleado' => $empleado->fresh('puesto:id,nombre')]);
    }

    public function reactivar(Request $request, Empleado $empleado)
    {
        $this->asegurarClinica($request, $empleado->clinica_id);

        if ($empleado->estado === 'activo') {
            return response()->json(['message' => 'El empleado ya está activo.'], 422);
        }

        $data = $request->validate([
            'fecha' => 'nullable|date',
        ], $this->mensajes(), $this->atributos());

        DB::transaction(function () use ($empleado, $data, $request) {
            $empleado->update(['estado' => 'activo', 'fecha_baja' => null, 'motivo_baja' => null]);

            $empleado->registrarHistorial('reingreso', 'Reingreso a la clínica.', [
                'fecha'          => $data['fecha'] ?? Carbon::today()->toDateString(),
                'registrado_por' => $this->registradoPor($request),
            ]);
        });

        return response()->json(['message' => 'Empleado reactivado', 'empleado' => $empleado->fresh('puesto:id,nombre')]);
    }

    /* ------------------------------------------------------------ */

    private function reglas(int $clinicaId, ?Empleado $empleado = null): array
    {
        $ignorar = fn ($rule) => $empleado ? $rule->ignore($empleado->id) : $rule;

        return [
            'puesto_id' => ['required', 'integer',
                Rule::exists('puestos', 'id')->where('clinica_id', $clinicaId)],
            'nombre'    => 'required|string|max:100',
            'apellidos' => 'required|string|max:120',
            'correo'    => 'nullable|email|max:150',
            'telefono'  => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'curp'      => ['nullable', 'string', 'size:18',
                $ignorar(Rule::unique('empleados', 'curp')->where('clinica_id', $clinicaId))],
            'rfc'       => 'nullable|string|min:12|max:13',
            'nss'       => 'nullable|string|size:11',
            'direccion' => 'nullable|string|max:500',
            'contacto_emergencia_nombre'   => 'nullable|string|max:120',
            'contacto_emergencia_telefono' => 'nullable|string|max:20',
            'fecha_ingreso'   => 'required|date',
            'tipo_contrato'   => 'required|in:indeterminado,determinado,honorarios,practicas',
            'salario_mensual' => 'nullable|numeric|min:0|max:9999999999',
        ];
    }

    /** Cadenas vacías → null y CURP/RFC en mayúsculas. */
    private function normalizar(Request $request): void
    {
        $limpio = [];
        foreach (['correo', 'telefono', 'fecha_nacimiento', 'curp', 'rfc', 'nss', 'direccion',
                  'contacto_emergencia_nombre', 'contacto_emergencia_telefono', 'salario_mensual'] as $campo) {
            if ($request->has($campo)) {
                $v = $request->input($campo);
                $limpio[$campo] = (is_string($v) && trim($v) === '') ? null : $v;
            }
        }

        foreach (['curp', 'rfc'] as $campo) {
            if (!empty($limpio[$campo])) {
                $limpio[$campo] = strtoupper(trim($limpio[$campo]));
            }
        }

        $request->merge($limpio);
    }

    /** EMP-0001, EMP-0002… por clínica. */
    private function siguienteNumero(int $clinicaId): string
    {
        $max = Empleado::where('clinica_id', $clinicaId)
            ->lockForUpdate()
            ->pluck('numero_empleado')
            ->map(fn ($n) => (int) substr($n, 4))
            ->max() ?? 0;

        return 'EMP-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }
}