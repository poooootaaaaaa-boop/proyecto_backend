<?php

namespace App\Http\Controllers;

use App\Models\FormatoConsentimiento;
use Illuminate\Http\Request;

class FormatoConsentimientoController extends Controller
{
    /**
     * Listar todos los formatos
     */
    public function index()
    {
        $formatos = FormatoConsentimiento::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $formatos
        ], 200);
    }

    /**
     * Listar solo los formatos activos
     */
    public function activos()
    {
        $formatos = FormatoConsentimiento::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $formatos
        ], 200);
    }

    /**
     * Crear formato
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'required|string',
            'requiere_firma' => 'required|boolean'
        ]);

        $formato = FormatoConsentimiento::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'contenido' => $request->contenido,
            'requiere_firma' => $request->requiere_firma,
            'activo' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Formato creado correctamente.',
            'data' => $formato
        ], 201);
    }

    /**
     * Mostrar un formato
     */
    public function show($id)
    {
        $formato = FormatoConsentimiento::find($id);

        if (!$formato) {
            return response()->json([
                'success' => false,
                'message' => 'Formato no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $formato
        ], 200);
    }

    /**
     * Actualizar formato
     */
    public function update(Request $request, $id)
    {
        $formato = FormatoConsentimiento::find($id);

        if (!$formato) {
            return response()->json([
                'success' => false,
                'message' => 'Formato no encontrado.'
            ], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'required|string',
            'requiere_firma' => 'required|boolean',
            'activo' => 'required|boolean'
        ]);

        $formato->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'contenido' => $request->contenido,
            'requiere_firma' => $request->requiere_firma,
            'activo' => $request->activo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Formato actualizado correctamente.',
            'data' => $formato
        ], 200);
    }

    /**
     * Activar o desactivar formato
     */
    public function cambiarEstado($id)
    {
        $formato = FormatoConsentimiento::find($id);

        if (!$formato) {
            return response()->json([
                'success' => false,
                'message' => 'Formato no encontrado.'
            ], 404);
        }

        $formato->activo = !$formato->activo;
        $formato->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado.',
            'data' => $formato
        ]);
    }

    /**
     * Eliminar formato
     */
    public function destroy($id)
    {
        $formato = FormatoConsentimiento::find($id);

        if (!$formato) {
            return response()->json([
                'success' => false,
                'message' => 'Formato no encontrado.'
            ], 404);
        }

        $formato->delete();

        return response()->json([
            'success' => true,
            'message' => 'Formato eliminado correctamente.'
        ], 200);
    }
}