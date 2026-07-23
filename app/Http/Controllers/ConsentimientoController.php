<?php

namespace App\Http\Controllers;

use App\Models\Consentimiento;
use App\Models\ConsentimientoHistorial;
use App\Models\FormatoConsentimiento;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConsentimientoController extends Controller
{
    /**
     * Listar todos los consentimientos
     */
    public function index()
    {
        $consentimientos = Consentimiento::with([
            'paciente',
            'doctor',
            'formato',
            'consulta'
        ])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $consentimientos
        ], 200);
    }

    /**
     * Mostrar un consentimiento (incluye adjuntos e historial completo)
     */
    public function show($id)
    {
        $consentimiento = Consentimiento::with([
            'paciente',
            'doctor',
            'formato',
            'consulta',
            'adjuntos',
            'historial' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->find($id);

        if (!$consentimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Consentimiento no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $consentimiento
        ], 200);
    }

    /**
     * Listar únicamente el historial de un consentimiento
     */
    public function historial($id)
    {
        $consentimiento = Consentimiento::find($id);

        if (!$consentimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Consentimiento no encontrado.'
            ], 404);
        }

        $historial = ConsentimientoHistorial::with('usuario')
            ->where('consentimiento_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $historial
        ], 200);
    }

    /**
     * Crear consentimiento
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'doctor_id' => 'required|exists:doctores,id',
            'formato_id' => 'required|exists:formatos_consentimiento,id',
            'consulta_id' => 'nullable|exists:consultas,id',
            'observaciones' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $formato = FormatoConsentimiento::find($request->formato_id);

        if (!$formato) {
            return response()->json([
                'success' => false,
                'message' => 'Formato no encontrado.'
            ], 404);
        }

        // El doctor seleccionado en el formulario es quien "actúa" en el
        // historial (en vez de auth()->id(), que aquí no está disponible
        // porque esta ruta no pasa por el middleware de Sanctum).
        $doctor = Doctor::find($request->doctor_id);

        $consentimiento = Consentimiento::create([
            'paciente_id' => $request->paciente_id,
            'doctor_id' => $request->doctor_id,
            'formato_id' => $request->formato_id,
            'consulta_id' => $request->consulta_id,
            'titulo' => $formato->nombre,
            'contenido' => $formato->contenido,
            'firma' => null,
            'pdf' => null,
            'estado' => 'Pendiente',
            'fecha_firma' => null,
            'observaciones' => $request->observaciones
        ]);

        $this->registrarHistorial(
            $consentimiento->id,
            'Creado',
            'Se generó el consentimiento a partir del formato "' . $formato->nombre . '".',
            $doctor->usuario_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Consentimiento creado correctamente.',
            'data' => $consentimiento
        ], 201);
    }

    /**
     * Guardar la firma electrónica de un consentimiento
     * Guarda la imagen en disco, actualiza la base de datos
     * y registra el evento en el historial.
     */
    public function guardarFirma(Request $request, $id)
    {
        $request->validate([
            'firma' => 'required|string'
        ]);

        $consentimiento = Consentimiento::find($id);

        if (!$consentimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Consentimiento no encontrado.'
            ], 404);
        }

        if ($consentimiento->estado === 'Firmado') {
            return response()->json([
                'success' => false,
                'message' => 'Este consentimiento ya fue firmado.'
            ], 409);
        }

        $imagen = $request->firma;
        $imagen = str_replace('data:image/png;base64,', '', $imagen);
        $imagen = str_replace(' ', '+', $imagen);

        $datosDecodificados = base64_decode($imagen, true);

        if ($datosDecodificados === false) {
            return response()->json([
                'success' => false,
                'message' => 'La imagen de la firma no es válida.'
            ], 422);
        }

        $nombre = 'firma_' . Str::uuid() . '.png';

        Storage::disk('public')->put(
            'firmas/' . $nombre,
            $datosDecodificados
        );

        $consentimiento->firma = 'firmas/' . $nombre;
        $consentimiento->estado = 'Firmado';
        $consentimiento->fecha_firma = Carbon::now();
        $consentimiento->save();

        // Igual que en store(): usamos el usuario_id del doctor dueño del
        // consentimiento, ya que no hay usuario autenticado vía Sanctum aquí.
        $doctor = Doctor::find($consentimiento->doctor_id);

        $this->registrarHistorial(
            $consentimiento->id,
            'Firmado',
            'El consentimiento fue firmado electrónicamente el ' .
                $consentimiento->fecha_firma->format('d/m/Y H:i') . '.',
            $doctor->usuario_id
        );

        $this->generarPDF($consentimiento->id);

        $this->registrarHistorial(
            $consentimiento->id,
            'PDF generado',
            'Se generó el documento PDF final con la firma incluida.',
            $doctor->usuario_id
        );

        $consentimiento->refresh()->load('historial', 'adjuntos');

        return response()->json([
            'success' => true,
            'message' => 'Firma guardada correctamente.',
            'data' => $consentimiento
        ], 200);
    }

    /**
     * Inserta un registro en consentimiento_historial.
     * Centralizado aquí para no repetir el mismo bloque en cada acción.
     *
     * $usuarioId debe ser siempre el id de la tabla `usuarios` (no el id
     * de `doctores` ni de `pacientes`), porque así lo exige la FK/columna
     * usuario_id de consentimiento_historial.
     */
    private function registrarHistorial($consentimientoId, $accion, $descripcion, $usuarioId)
    {
        ConsentimientoHistorial::create([
            'consentimiento_id' => $consentimientoId,
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'created_at' => Carbon::now()
        ]);
    }

    /**
     * Genera el PDF final del consentimiento firmado.
     * (Se asume que ya existe esta lógica en tu proyecto;
     * se deja el nombre del método para mantener compatibilidad).
     */
    private function generarPDF($id)
    {
        $consentimiento = Consentimiento::with(['paciente', 'doctor', 'formato'])
            ->find($id);

        $pdf = Pdf::loadView('pdf.consentimiento', [
            'consentimiento' => $consentimiento
        ]);

        $nombrePdf = 'consentimiento_' . $consentimiento->id . '.pdf';
        Storage::disk('public')->put('pdfs/' . $nombrePdf, $pdf->output());

        $consentimiento->pdf = 'pdfs/' . $nombrePdf;
        $consentimiento->save();
    }
}