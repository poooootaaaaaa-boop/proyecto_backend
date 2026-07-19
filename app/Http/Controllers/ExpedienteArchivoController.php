<?php

namespace App\Http\Controllers;

use App\Models\ExpedienteArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpedienteArchivoController extends Controller
{
    /**
     * GET /expediente-archivos/paciente/{id}
     * Lista todos los archivos del expediente de un paciente (más reciente primero).
     */
    public function porPaciente($id)
    {
        $archivos = ExpedienteArchivo::where('paciente_id', $id)
            ->orderBy('creado_en', 'desc')
            ->get()
            ->map(function ($archivo) {
                return [
                    'id'             => $archivo->id,
                    'nombre_archivo' => $archivo->nombre_archivo,
                    'url_archivo'    => $archivo->url_archivo
                        ? asset('storage/' . $archivo->url_archivo)
                        : null,
                    'tipo'           => $archivo->tipo, // 'pdf' | 'image'
                    'creado_en'      => $archivo->creado_en,
                ];
            });

        return response()->json($archivos);
    }

    /**
     * POST /expediente-archivos
     * Sube uno o varios archivos (multipart/form-data) y los asocia al paciente.
     *
     * Campos esperados en el form-data:
     *   paciente_id  (required)
     *   consulta_id  (opcional)
     *   archivos[]   (required, uno o más archivos)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'consulta_id' => 'nullable|exists:consultas,id',
            'archivos'    => 'required|array|min:1',
            'archivos.*'  => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guardados = [];

        foreach ($request->file('archivos') as $archivo) {
            $extension = strtolower($archivo->getClientOriginalExtension());
            $tipo = $extension === 'pdf' ? 'pdf' : 'image';

            // Se guarda en storage/app/public/expedientes/{paciente_id}/...
            $ruta = $archivo->store('expedientes/' . $request->paciente_id, 'public');

            $registro = ExpedienteArchivo::create([
                'paciente_id'    => $request->paciente_id,
                'consulta_id'    => $request->consulta_id,
                'nombre_archivo' => $archivo->getClientOriginalName(),
                'url_archivo'    => $ruta,
                'tipo'           => $tipo,
                'creado_en'      => now(),
            ]);

            $guardados[] = [
                'id'             => $registro->id,
                'nombre_archivo' => $registro->nombre_archivo,
                'url_archivo'    => asset('storage/' . $ruta),
                'tipo'           => $registro->tipo,
                'creado_en'      => $registro->creado_en,
            ];
        }

        return response()->json([
            'message'  => 'Archivos subidos correctamente',
            'archivos' => $guardados,
        ], 201);
    }

    /**
     * DELETE /expediente-archivos/{id}
     * Elimina el archivo físico y su registro.
     */
    public function destroy($id)
    {
        $archivo = ExpedienteArchivo::findOrFail($id);

        if ($archivo->url_archivo) {
            Storage::disk('public')->delete($archivo->url_archivo);
        }

        $archivo->delete();

        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }
}
