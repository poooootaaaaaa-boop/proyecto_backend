<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesPorClinica;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** CLIC-386 · Registrar puesto */
class PuestoController extends Controller
{
    use ScopesPorClinica;

    public function index(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $puestos = Puesto::where('clinica_id', $clinicaId)
            ->withCount(['empleados as empleados_activos' => fn ($q) => $q->where('estado', 'activo')])
            ->orderBy('nombre')
            ->get();

        return response()->json($puestos);
    }

    public function store(Request $request)
    {
        $clinicaId = $this->clinicaId($request);

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100',
                Rule::unique('puestos', 'nombre')->where('clinica_id', $clinicaId)],
            'descripcion'  => 'nullable|string|max:1000',
            'salario_base' => 'nullable|numeric|min:0|max:9999999999',
            'activo'       => 'nullable|boolean',
        ], $this->mensajes(), $this->atributos());

        $puesto = Puesto::create(array_merge($data, ['clinica_id' => $clinicaId, 'activo' => $data['activo'] ?? true]));

        return response()->json(['message' => 'Puesto registrado correctamente', 'puesto' => $puesto], 201);
    }

    public function update(Request $request, Puesto $puesto)
    {
        $this->asegurarClinica($request, $puesto->clinica_id);

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100',
                Rule::unique('puestos', 'nombre')->where('clinica_id', $puesto->clinica_id)->ignore($puesto->id)],
            'descripcion'  => 'nullable|string|max:1000',
            'salario_base' => 'nullable|numeric|min:0|max:9999999999',
            'activo'       => 'nullable|boolean',
        ], $this->mensajes(), $this->atributos());

        $puesto->update($data);

        return response()->json(['message' => 'Puesto actualizado', 'puesto' => $puesto]);
    }

    public function destroy(Request $request, Puesto $puesto)
    {
        $this->asegurarClinica($request, $puesto->clinica_id);

        if ($puesto->empleados()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: hay empleados asignados a este puesto. Desactívalo en su lugar.',
            ], 422);
        }

        $puesto->delete();

        return response()->json(['message' => 'Puesto eliminado']);
    }
}