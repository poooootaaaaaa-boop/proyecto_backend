<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\HistorialLaboral;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CLIC-390 · Historial laboral */
class HistorialLaboralController extends Controller
{
    use ScopesPorClinica;

    /** Eventos que el usuario puede registrar a mano (los demás los genera el sistema). */
    private const MANUALES = ['reconocimiento', 'amonestacion', 'capacitacion', 'otro'];

    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $query = HistorialLaboral::with('empleado:id,numero_empleado,nombre,apellidos')
            ->whereHas('empleado', fn ($q) => $q->where('clinica_id', $clinicaId));

        foreach (['empleado_id', 'tipo'] as $filtro) {
            if ($request->filled($filtro)) {
                $query->where($filtro, $request->input($filtro));
            }
        }

        return response()->json(
            $query->orderByDesc('fecha')->orderByDesc('id')->limit(500)->get()
        );
    }

    public function store(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $data = $request->validate([
            'empleado_id' => ['required', 'integer',
                Rule::exists('empleados', 'id')->where('clinica_id', $clinicaId)],
            'tipo'        => ['required', Rule::in(self::MANUALES)],
            'fecha'       => 'required|date',
            'descripcion' => 'required|string|max:500',
        ], $this->mensajes(), $this->atributos());

        $evento = HistorialLaboral::create($data + [
            'automatico'     => false,
            'registrado_por' => $this->registradoPor($request),
        ]);

        return response()->json([
            'message' => 'Evento registrado en el historial laboral',
            'evento'  => $evento->load('empleado:id,numero_empleado,nombre,apellidos'),
        ], 201);
    }
}