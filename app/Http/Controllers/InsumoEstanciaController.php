<?php

namespace App\Http\Controllers;

use App\Models\InsumoEstancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsumoEstanciaController extends Controller
{
    public function index(Request $request)
    {
        $query = InsumoEstancia::query()->orderBy('categoria')->orderBy('nombre');

        if (!$request->boolean('incluir_inactivos')) {
            $query->where('activo', true);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->input('categoria'));
        }

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where('nombre', 'like', "%{$buscar}%");
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:180',
            'categoria' => 'required|in:medicamento,suero,material_medico',
            'unidad_medida' => 'required|string|max:60',
            'precio_unitario' => 'required|numeric|min:0|max:9999999999.99',
            'activo' => 'sometimes|boolean',
        ]);

        $insumo = InsumoEstancia::create($datos);

        return response()->json(['data' => $insumo->fresh()], 201);
    }

    public function show(InsumoEstancia $insumoEstancia)
    {
        return response()->json(['data' => $insumoEstancia]);
    }

    public function update(Request $request, InsumoEstancia $insumoEstancia)
    {
        $datos = $request->validate([
            'nombre' => 'sometimes|required|string|max:180',
            'categoria' => 'sometimes|required|in:medicamento,suero,material_medico',
            'unidad_medida' => 'sometimes|required|string|max:60',
            'precio_unitario' => 'sometimes|required|numeric|min:0|max:9999999999.99',
            'activo' => 'sometimes|boolean',
        ]);

        $insumoEstancia->update($datos);

        return response()->json(['data' => $insumoEstancia->fresh()]);
    }

    public function destroy(InsumoEstancia $insumoEstancia)
    {
        $insumoEstancia->update(['activo' => false]);

        return response()->json(['message' => 'Insumo desactivado.', 'data' => $insumoEstancia]);
    }

    public function doctoresVinculados(int $ocupacion)
{
    $existe = DB::table('ocupacion_habitaciones')->where('id', $ocupacion)->exists();

    if (!$existe) {
        return response()->json(['message' => 'Estancia no encontrada.'], 404);
    }

    // Consentimientos ligados a esta estancia
    $consentimientos = DB::table('consentimiento_hospitalizacion as ch')
        ->join('consentimientos as c', 'c.id', '=', 'ch.consentimiento_id')
        ->where('ch.ocupacion_id', $ocupacion)
        ->select('c.id', 'c.doctor_id')
        ->get();

    $responsables = $consentimientos->pluck('doctor_id')->unique();

    $vigilancia = DB::table('consentimiento_vigilancia')
        ->whereIn('consentimiento_id', $consentimientos->pluck('id'))
        ->pluck('doctor_id')
        ->unique();

    $idsDoctores = $responsables->merge($vigilancia)->unique()->values();

    $doctores = DB::table('doctores as d')
        ->join('usuarios as u', 'u.id', '=', 'd.usuario_id')
        ->leftJoin('especialidades as e', 'e.id', '=', 'd.especialidad_id')
        ->whereIn('d.id', $idsDoctores)
        ->select('d.id', 'u.nombre', 'u.correo', 'e.nombre as especialidad')
        ->orderBy('u.nombre')
        ->get()
        ->map(function ($doctor) use ($responsables) {
            $doctor->rol = $responsables->contains($doctor->id) ? 'responsable' : 'vigilancia';
            return $doctor;
        })
        // el responsable siempre primero
        ->sortBy(fn ($doctor) => $doctor->rol === 'responsable' ? 0 : 1)
        ->values();

    return response()->json(['data' => $doctores]);
}
}