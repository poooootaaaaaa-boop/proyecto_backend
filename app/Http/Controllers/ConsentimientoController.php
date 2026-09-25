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
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ConsentimientoController extends Controller
{
    /**
     * Tipos de hospitalización disponibles.
     */
    private const TIPOS_HOSPITALIZACION = [
        'urgencias'   => 'Observación en urgencias',
        'general'     => 'Hospitalización general',
        'uci'         => 'Cuidados intensivos',
        'quirurgica'  => 'Quirúrgica',
        'obstetricia' => 'Obstetricia',
        'pediatria'   => 'Pediatría',
    ];

    /**
     * Tipos de firma disponibles.
     */
    private const TIPOS_FIRMA = [
        'Clinica',
        'Doctor',
        'Paciente'
    ];

    /**
     * ============================================================
     * LISTAR CONSENTIMIENTOS
     * ============================================================
     */
    public function index(Request $request)
    {
        $query = Consentimiento::with([
            'paciente',
            'doctor',
            'formato',
            'consulta'
        ])->orderBy('created_at', 'desc');

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $consentimientos = $query->get();

        return response()->json([
            'success' => true,
            'data' => $consentimientos
        ], 200);
    }

    /**
     * ============================================================
     * MOSTRAR CONSENTIMIENTO
     * ============================================================
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

        $detalle = $this->detalle((int) $id);

        return response()->json([
            'success' => true,
            'data' => $detalle
        ], 200);
    }

    /**
     * ============================================================
     * HISTORIAL
     * ============================================================
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
     * ============================================================
     * CREAR CONSENTIMIENTO
     * ============================================================
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'doctor_id' => 'required|integer|exists:doctores,id',
            'formato_id' => 'required|integer|exists:formatos_consentimiento,id',
            'consulta_id' => 'nullable|integer|exists:consultas,id',
            'observaciones' => 'nullable|string',

            // Hospitalización
            'hospitalizacion' => 'nullable|array',
            'hospitalizacion.tipo' => 'required_with:hospitalizacion|in:' .
                implode(',', array_keys(self::TIPOS_HOSPITALIZACION)),
            'hospitalizacion.prioridad' => 'nullable|in:Programada,Urgente,Crítica',
            'hospitalizacion.fecha_ingreso' => 'nullable|date',
            'hospitalizacion.dias_estimados' => 'nullable|integer|min:1|max:365',
            'hospitalizacion.habitacion_id' => 'nullable|integer|exists:habitaciones,id',
            'hospitalizacion.diagnostico' => 'nullable|string|max:255',
            'hospitalizacion.motivo' => 'nullable|string',

            // Doctores de vigilancia
            'doctores_vigilancia' => 'nullable|array',
            'doctores_vigilancia.*' => 'integer|exists:doctores,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $id = DB::transaction(function () use ($request) {

                $now = now();

                $formato = FormatoConsentimiento::find($request->formato_id);

                if (!$formato) {
                    abort(404, 'Formato no encontrado.');
                }

                $doctor = Doctor::find($request->doctor_id);

                if (!$doctor) {
                    abort(404, 'Doctor no encontrado.');
                }

                /*
                 * Obtener información adicional del doctor.
                 */
                $doctorData = DB::table('doctores as d')
                    ->join('usuarios as u', 'u.id', '=', 'd.usuario_id')
                    ->where('d.id', $request->doctor_id)
                    ->select(
                        'd.id',
                        'd.clinica_id',
                        'd.usuario_id',
                        'u.nombre'
                    )
                    ->first();

                /*
                 * Obtener paciente.
                 */
                $pacienteData = DB::table('pacientes as p')
                    ->join('usuarios as u', 'u.id', '=', 'p.usuario_id')
                    ->where('p.id', $request->paciente_id)
                    ->select(
                        'p.id',
                        'u.nombre'
                    )
                    ->first();

                /*
                 * Obtener clínica.
                 */
                $clinica = null;

                if ($doctorData && $doctorData->clinica_id) {
                    $clinica = DB::table('clinicas')
                        ->where('id', $doctorData->clinica_id)
                        ->first();
                }

                /*
                 * Hospitalización.
                 */
                $hosp = $request->input('hospitalizacion');

                $habitacion = null;

                if (!empty($hosp['habitacion_id'])) {

                    $habitacion = DB::table('habitaciones')
                        ->where('id', $hosp['habitacion_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$habitacion) {
                        abort(422, 'La habitación seleccionada no existe.');
                    }

                    if (
                        isset($habitacion->estado) &&
                        $habitacion->estado !== 'Disponible'
                    ) {
                        abort(
                            422,
                            'La habitación seleccionada ya no está disponible.'
                        );
                    }
                }

                /*
                 * Reemplazar marcadores del formato.
                 */
                $contenido = $this->reemplazarMarcadores(
                    $formato->contenido,
                    [
                        'paciente' => $pacienteData->nombre ?? '',
                        'doctor' => $doctorData->nombre ?? '',
                        'clinica' => $clinica->nombre ?? '',
                        'fecha' => $now->format('d/m/Y'),

                        'tipo_hospitalizacion' =>
                            $hosp
                                ? (self::TIPOS_HOSPITALIZACION[$hosp['tipo']] ?? '')
                                : '',

                        'fecha_ingreso' =>
                            !empty($hosp['fecha_ingreso'])
                                ? date(
                                    'd/m/Y',
                                    strtotime($hosp['fecha_ingreso'])
                                )
                                : '',

                        'diagnostico' => $hosp['diagnostico'] ?? '',
                        'motivo' => $hosp['motivo'] ?? '',

                        'habitacion' =>
                            $habitacion
                                ? ('Hab. ' . $habitacion->numero)
                                : '',
                    ]
                );

                /*
                 * Crear consentimiento usando Eloquent.
                 * Conservamos tu estructura original.
                 */
                $consentimiento = Consentimiento::create([
                    'paciente_id' => $request->paciente_id,
                    'doctor_id' => $request->doctor_id,
                    'formato_id' => $request->formato_id,
                    'consulta_id' => $request->consulta_id,
                    'titulo' => $formato->nombre,
                    'contenido' => $contenido,
                    'firma' => null,
                    'pdf' => null,
                    'estado' => 'Pendiente',
                    'fecha_firma' => null,
                    'observaciones' => $request->observaciones
                ]);

                /*
                 * Historial.
                 */
                $this->registrarHistorial(
                    $consentimiento->id,
                    'Creado',
                    'Se generó el consentimiento a partir del formato "' .
                        $formato->nombre .
                        '".',
                    $doctorData->usuario_id
                );

                /*
                 * ========================================================
                 * COPIAR FIRMA REGISTRADA DE CLÍNICA Y DOCTOR
                 * ========================================================
                 */
                foreach (
                    [
                        ['Clinica', $doctorData->clinica_id ?? null],
                        ['Doctor', $doctorData->id]
                    ] as [$tipo, $referenciaId]
                ) {

                    if (!$referenciaId) {
                        continue;
                    }

                    $firmaRegistrada = DB::table('firmas_registradas')
                        ->where('tipo', $tipo)
                        ->where('referencia_id', $referenciaId)
                        ->first([
                            'firma',
                            'mime'
                        ]);

                    if ($firmaRegistrada) {

                        DB::table('consentimiento_firmas')->insert([
                            'consentimiento_id' => $consentimiento->id,
                            'tipo' => $tipo,
                            'firma' => $firmaRegistrada->firma,
                            'mime' => $firmaRegistrada->mime,
                            'firmado_en' => $now,
                            'created_at' => $now,
                            'updated_at' => $now
                        ]);
                    }
                }

                /*
                 * ========================================================
                 * HOSPITALIZACIÓN
                 * ========================================================
                 */
                if ($hosp) {

                    $ocupacionId = null;

                    /*
                     * Crear ocupación de habitación.
                     */
             if ($habitacion) {

    $ocupacionId = DB::table(
        'ocupacion_habitaciones'
    )->insertGetId([
        'habitacion_id' => $habitacion->id,
        'paciente_id' => $request->paciente_id,
        'fecha_ingreso' =>
            $hosp['fecha_ingreso'] ?? $now,
        'estado' => 'Activa',
    ]);

    /*
     * Marcar habitación como ocupada.
     */
    DB::table('habitaciones')
        ->where('id', $habitacion->id)
        ->update([
            'estado' => 'Ocupada',
            'updated_at' => $now
        ]);
}

                    /*
                     * Crear información de hospitalización.
                     */
                    DB::table(
                        'consentimiento_hospitalizacion'
                    )->insert([
                        'consentimiento_id' => $consentimiento->id,
                        'tipo' => $hosp['tipo'],
                        'prioridad' =>
                            $hosp['prioridad'] ?? 'Programada',
                        'fecha_ingreso' =>
                            $hosp['fecha_ingreso'] ?? null,
                        'dias_estimados' =>
                            $hosp['dias_estimados'] ?? null,
                        'habitacion_id' =>
                            $habitacion->id ?? null,
                        'ocupacion_id' => $ocupacionId,
                        'diagnostico' =>
                            $hosp['diagnostico'] ?? null,
                        'motivo' =>
                            $hosp['motivo'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now
                    ]);

                    /*
                     * Doctores de vigilancia.
                     */
                    $vigilantes = collect(
                        $request->input('doctores_vigilancia', [])
                    )
                        ->map(fn($v) => (int) $v)
                        ->unique()
                        ->reject(
                            fn($v) =>
                                $v === (int) $request->doctor_id
                        );

                    foreach ($vigilantes as $doctorVigilaId) {

                        DB::table(
                            'consentimiento_vigilancia'
                        )->insert([
                            'consentimiento_id' =>
                                $consentimiento->id,
                            'doctor_id' =>
                                $doctorVigilaId,
                            'created_at' => $now
                        ]);
                    }

                    $descripcion =
                        (
                            self::TIPOS_HOSPITALIZACION[
                                $hosp['tipo']
                            ] ?? $hosp['tipo']
                        )
                        . ' (' .
                        ($hosp['prioridad'] ?? 'Programada')
                        . ')';

                    if ($vigilantes->count()) {
                        $descripcion .=
                            ' · ' .
                            $vigilantes->count() .
                            ' doctor(es) de vigilancia';
                    }

                    $this->registrarHistorial(
                        $consentimiento->id,
                        'Hospitalización',
                        $descripcion,
                        $doctorData->usuario_id
                    );
                }

                return $consentimiento->id;
            });

            return response()->json([
                'success' => true,
                'message' => 'Consentimiento creado correctamente.',
                'data' => $this->detalle($id)
            ], 201);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * ============================================================
     * GUARDAR FIRMA
     * ============================================================
     *
     * tipo:
     * - Paciente
     * - Doctor
     * - Clinica
     */
    public function guardarFirma(Request $request, $id)
    {
        $data = $request->validate([
            'firma' => 'required|string',
            'tipo' => 'nullable|in:Paciente,Doctor,Clinica',
            'registrar' => 'nullable|boolean',
        ]);

        $tipo = $data['tipo'] ?? 'Paciente';

        $consentimiento = Consentimiento::find($id);

        if (!$consentimiento) {
            return response()->json([
                'success' => false,
                'message' => 'Consentimiento no encontrado.'
            ], 404);
        }

        if ($consentimiento->estado === 'Cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'El consentimiento está cancelado.'
            ], 422);
        }

        /*
         * Para conservar el comportamiento anterior:
         * si el paciente ya firmó, no permitir otra firma de paciente.
         */
        if (
            $tipo === 'Paciente' &&
            $consentimiento->estado === 'Firmado'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Este consentimiento ya fue firmado.'
            ], 409);
        }

        /*
         * Obtener doctor y clínica.
         */
        $doctorData = DB::table('doctores as d')
            ->where('d.id', $consentimiento->doctor_id)
            ->select(
                'd.id',
                'd.clinica_id',
                'd.usuario_id'
            )
            ->first();

        if (!$doctorData) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor del consentimiento no encontrado.'
            ], 404);
        }

        /*
         * Decodificar imagen.
         */
        [$binario, $mime] = $this->decodificarImagen(
            $data['firma']
        );

        DB::transaction(function () use (
            $consentimiento,
            $tipo,
            $binario,
            $mime,
            $data,
            $doctorData
        ) {

            $now = now();

            /*
             * ========================================================
             * MANTENER EL ARCHIVO EN STORAGE
             * ========================================================
             *
             * Esto conserva tu funcionamiento anterior.
             */
            $nombre = 'firma_' . Str::uuid() . '.png';

            Storage::disk('public')->put(
                'firmas/' . $nombre,
                $binario
            );

            /*
             * ========================================================
             * GUARDAR FIRMA EN consentimiento_firmas
             * ========================================================
             */
            DB::table('consentimiento_firmas')->updateOrInsert(
                [
                    'consentimiento_id' =>
                        $consentimiento->id,
                    'tipo' => $tipo
                ],
                [
                    'firma' => $binario,
                    'mime' => $mime,
                    'firmado_en' => $now,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            );

            /*
             * ========================================================
             * ACTUALIZAR CONSENTIMIENTO
             * ========================================================
             */
            if ($tipo === 'Paciente') {

                $consentimiento->firma =
                    'firmas/' . $nombre;

                $consentimiento->estado = 'Firmado';

                $consentimiento->fecha_firma = $now;

                $consentimiento->save();
            }

            /*
             * ========================================================
             * REGISTRAR FIRMA REUTILIZABLE
             * ========================================================
             */
            if (
                !empty($data['registrar']) &&
                in_array(
                    $tipo,
                    ['Clinica', 'Doctor'],
                    true
                )
            ) {

                $referenciaId =
                    $tipo === 'Clinica'
                        ? $doctorData->clinica_id
                        : $doctorData->id;

                if ($referenciaId) {

                    DB::table('firmas_registradas')
                        ->updateOrInsert(
                            [
                                'tipo' => $tipo,
                                'referencia_id' =>
                                    $referenciaId
                            ],
                            [
                                'firma' => $binario,
                                'mime' => $mime,
                                'created_at' => $now,
                                'updated_at' => $now
                            ]
                        );
                }
            }

            /*
             * Historial.
             */
            $accion = [
                'Paciente' => 'Firmado',
                'Doctor' => 'Firma del doctor',
                'Clinica' => 'Firma de la clínica'
            ][$tipo];

            $descripcion =
                'Se guardó la firma de: ' .
                strtolower($tipo) .
                '.';

            $this->registrarHistorial(
                $consentimiento->id,
                $accion,
                $descripcion,
                $doctorData->usuario_id
            );
        });

        /*
         * Regenerar PDF con las firmas actuales.
         */
        $this->generarPDF($consentimiento->id);

        /*
         * Historial de PDF.
         */
        $this->registrarHistorial(
            $consentimiento->id,
            'PDF generado',
            'Se generó el documento PDF con las firmas actuales.',
            $doctorData->usuario_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Firma guardada correctamente.',
            'data' => $this->detalle((int) $consentimiento->id)
        ], 200);
    }

    /**
     * ============================================================
     * MOSTRAR IMAGEN DE FIRMA
     * ============================================================
     */
    public function firmaImagen($id, $tipo)
    {
        $tipo = ucfirst(strtolower($tipo));

        if (!in_array($tipo, self::TIPOS_FIRMA, true)) {
            abort(
                422,
                'Tipo de firma inválido.'
            );
        }

        $row = DB::table('consentimiento_firmas')
            ->where('consentimiento_id', $id)
            ->where('tipo', $tipo)
            ->first([
                'firma',
                'mime'
            ]);

        abort_if(
            !$row,
            404,
            'Firma no encontrada.'
        );

        return response(
            $row->firma,
            200,
            [
                'Content-Type' =>
                    $row->mime,
                'Cache-Control' =>
                    'private, max-age=0, must-revalidate'
            ]
        );
    }

    /**
     * ============================================================
     * PDF
     * ============================================================
     */
    public function pdf(Request $request, $id)
    {
        $binario = $this->generarPDF(
            (int) $id
        );

        $modo =
            $request->boolean('download')
                ? 'attachment'
                : 'inline';

        return response(
            $binario,
            200,
            [
                'Content-Type' =>
                    'application/pdf',

                'Content-Disposition' =>
                    $modo .
                    '; filename="consentimiento_' .
                    (int) $id .
                    '.pdf"'
            ]
        );
    }

    /**
     * ============================================================
     * SUBIR ADJUNTO
     * ============================================================
     */
    public function subirAdjunto(Request $request, $id)
    {
        $request->validate([
            'archivo' =>
                'required|file|max:5120|mimes:pdf,jpg,jpeg,png,webp,doc,docx',

            'tipo' =>
                'nullable|string|max:100'
        ]);

        $consentimiento = DB::table(
            'consentimientos as c'
        )
            ->join(
                'doctores as d',
                'd.id',
                '=',
                'c.doctor_id'
            )
            ->where('c.id', $id)
            ->select(
                'c.id',
                'd.usuario_id as doctor_usuario_id'
            )
            ->first();

        abort_if(
            !$consentimiento,
            404,
            'Consentimiento no encontrado.'
        );

        $archivo = $request->file('archivo');

        $nombre = str_replace(
            ['"', "\r", "\n"],
            '',
            $archivo->getClientOriginalName()
        );

        $now = now();

        DB::table(
            'consentimiento_adjuntos'
        )->insert([
            'consentimiento_id' =>
                $consentimiento->id,

            'archivo' =>
                $nombre,

            'nombre_original' =>
                $nombre,

            'tipo' =>
                $request->input('tipo'),

            'mime' =>
                $archivo->getMimeType()
                ?: 'application/octet-stream',

            'tamano' =>
                $archivo->getSize(),

            'contenido' =>
                file_get_contents(
                    $archivo->getRealPath()
                ),

            'created_at' =>
                $now,

            'updated_at' =>
                $now
        ]);

        $this->registrarHistorial(
            $consentimiento->id,
            'Adjunto',
            'Se adjuntó el archivo "' .
                $nombre .
                '".',
            $consentimiento->doctor_usuario_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Adjunto subido correctamente.',
            'data' =>
                $this->detalle(
                    (int) $consentimiento->id
                )
        ], 201);
    }

    /**
     * ============================================================
     * DESCARGAR ADJUNTO
     * ============================================================
     */
    public function descargarAdjunto($adjuntoId)
    {
        $row = DB::table(
            'consentimiento_adjuntos'
        )
            ->where('id', $adjuntoId)
            ->first([
                'nombre_original',
                'archivo',
                'mime',
                'contenido'
            ]);

        abort_if(
            !$row ||
            $row->contenido === null,
            404,
            'Adjunto no encontrado.'
        );

        $nombre = str_replace(
            ['"', "\r", "\n"],
            '',
            $row->nombre_original
                ?? $row->archivo
        );

        return response(
            $row->contenido,
            200,
            [
                'Content-Type' =>
                    $row->mime
                    ?: 'application/octet-stream',

                'Content-Disposition' =>
                    'inline; filename="' .
                    $nombre .
                    '"'
            ]
        );
    }

    /**
     * ============================================================
     * ELIMINAR ADJUNTO
     * ============================================================
     */
    public function eliminarAdjunto($adjuntoId)
    {
        $adjunto = DB::table(
            'consentimiento_adjuntos'
        )
            ->where('id', $adjuntoId)
            ->first([
                'id',
                'consentimiento_id',
                'nombre_original'
            ]);

        abort_if(
            !$adjunto,
            404,
            'Adjunto no encontrado.'
        );

        DB::table(
            'consentimiento_adjuntos'
        )
            ->where('id', $adjuntoId)
            ->delete();

        $doctor = DB::table(
            'consentimientos as c'
        )
            ->join(
                'doctores as d',
                'd.id',
                '=',
                'c.doctor_id'
            )
            ->where(
                'c.id',
                $adjunto->consentimiento_id
            )
            ->select(
                'd.usuario_id'
            )
            ->first();

        if ($doctor) {
            $this->registrarHistorial(
                $adjunto->consentimiento_id,
                'Adjunto eliminado',
                'Se eliminó el archivo "' .
                    $adjunto->nombre_original .
                    '".',
                $doctor->usuario_id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Adjunto eliminado.'
        ]);
    }

    /**
     * ============================================================
     * HISTORIAL INTERNO
     * ============================================================
     */
    private function registrarHistorial(
        $consentimientoId,
        $accion,
        $descripcion,
        $usuarioId
    ) {
        ConsentimientoHistorial::create([
            'consentimiento_id' =>
                $consentimientoId,

            'usuario_id' =>
                $usuarioId,

            'accion' =>
                $accion,

            'descripcion' =>
                $descripcion,

            'created_at' =>
                Carbon::now()
        ]);
    }

    /**
     * ============================================================
     * GENERAR PDF
     * ============================================================
     *
     * Se conserva el PDF en storage y además se guarda
     * una copia binaria en consentimiento_pdfs.
     */
    private function generarPDF($id)
    {
        $consentimiento = Consentimiento::with([
            'paciente',
            'doctor',
            'formato'
        ])->find($id);

        if (!$consentimiento) {
            abort(
                404,
                'Consentimiento no encontrado.'
            );
        }

        /*
         * Obtener detalle completo.
         */
        $c = json_decode(
            json_encode(
                $this->detalle((int) $id)
            )
        );

        /*
         * Obtener firmas.
         */
        $firmas = [];

        $filas = DB::table(
            'consentimiento_firmas'
        )
            ->where(
                'consentimiento_id',
                $id
            )
            ->get([
                'tipo',
                'firma',
                'mime'
            ]);

        foreach ($filas as $firma) {

            $firmas[
                strtolower($firma->tipo)
            ] =
                'data:' .
                $firma->mime .
                ';base64,' .
                base64_encode(
                    $firma->firma
                );
        }

        /*
         * Clínica.
         */
        $clinica = null;

        if (!empty($c->clinica_id)) {

            $clinica = DB::table(
                'clinicas'
            )
                ->where(
                    'id',
                    $c->clinica_id
                )
                ->first();
        }

        /*
         * Generar PDF.
         *
         * Pasamos las variables antiguas y nuevas
         * para mantener compatibilidad con tu vista.
         */
        $pdf = Pdf::loadView(
            'pdf.consentimiento',
            [
                'consentimiento' =>
                    $consentimiento,

                'c' =>
                    $c,

                'clinica' =>
                    $clinica,

                'firmas' =>
                    $firmas,

                'tiposHosp' =>
                    self::TIPOS_HOSPITALIZACION
            ]
        )->setPaper('letter');

        $binario = $pdf->output();

        /*
         * ========================================================
         * MANTENER PDF EN STORAGE
         * ========================================================
         */
        $nombrePdf =
            'consentimiento_' .
            $consentimiento->id .
            '.pdf';

        Storage::disk('public')->put(
            'pdfs/' . $nombrePdf,
            $binario
        );

        /*
         * Mantener referencia anterior.
         */
        $consentimiento->pdf =
            'pdfs/' . $nombrePdf;

        $consentimiento->save();

        /*
         * ========================================================
         * GUARDAR PDF EN BD
         * ========================================================
         */
        $now = now();

        DB::table(
            'consentimiento_pdfs'
        )->updateOrInsert(
            [
                'consentimiento_id' =>
                    $id
            ],
            [
                'contenido' =>
                    $binario,

                'mime' =>
                    'application/pdf',

                'tamano' =>
                    strlen($binario),

                'generado_en' =>
                    $now,

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now
            ]
        );

        return $binario;
    }

    /**
     * ============================================================
     * DETALLE COMPLETO
     * ============================================================
     */
    private function detalle(int $id): array
    {
        $c = DB::table(
            'consentimientos as c'
        )
            ->join(
                'pacientes as p',
                'p.id',
                '=',
                'c.paciente_id'
            )
            ->join(
                'usuarios as up',
                'up.id',
                '=',
                'p.usuario_id'
            )
            ->join(
                'doctores as d',
                'd.id',
                '=',
                'c.doctor_id'
            )
            ->join(
                'usuarios as ud',
                'ud.id',
                '=',
                'd.usuario_id'
            )
            ->leftJoin(
                'clinicas as cl',
                'cl.id',
                '=',
                'd.clinica_id'
            )
            ->where(
                'c.id',
                $id
            )
            ->select(
                'c.id',
                'c.paciente_id',
                'c.doctor_id',
                'c.formato_id',
                'c.consulta_id',
                'c.titulo',
                'c.contenido',
                'c.estado',
                'c.fecha_firma',
                'c.observaciones',
                'c.pdf',
                'c.created_at',

                'up.nombre as paciente_nombre',
                'ud.nombre as doctor_nombre',

                'cl.id as clinica_id',
                'cl.nombre as clinica_nombre'
            )
            ->first();

        abort_if(
            !$c,
            404,
            'Consentimiento no encontrado.'
        );

        $out = (array) $c;

        /*
         * Hospitalización.
         */
        $out['hospitalizacion'] =
            DB::table(
                'consentimiento_hospitalizacion as h'
            )
                ->leftJoin(
                    'habitaciones as hb',
                    'hb.id',
                    '=',
                    'h.habitacion_id'
                )
                ->where(
                    'h.consentimiento_id',
                    $id
                )
                ->select(
                    'h.tipo',
                    'h.prioridad',
                    'h.fecha_ingreso',
                    'h.dias_estimados',
                    'h.habitacion_id',
                    'h.diagnostico',
                    'h.motivo',

                    'hb.numero as habitacion_numero',
                    'hb.piso as habitacion_piso',
                    'hb.tipo as habitacion_tipo'
                )
                ->first();

        /*
         * Doctores de vigilancia.
         */
        $out['vigilancia'] =
            DB::table(
                'consentimiento_vigilancia as v'
            )
                ->join(
                    'doctores as d',
                    'd.id',
                    '=',
                    'v.doctor_id'
                )
                ->join(
                    'usuarios as u',
                    'u.id',
                    '=',
                    'd.usuario_id'
                )
                ->where(
                    'v.consentimiento_id',
                    $id
                )
                ->select(
                    'd.id as doctor_id',
                    'u.nombre'
                )
                ->get();

        /*
         * Firmas.
         */
        $out['firmas'] =
            DB::table(
                'consentimiento_firmas'
            )
                ->where(
                    'consentimiento_id',
                    $id
                )
                ->select(
                    'tipo',
                    'firmado_en'
                )
                ->get();

        /*
         * Adjuntos.
         */
        $out['adjuntos'] =
            DB::table(
                'consentimiento_adjuntos'
            )
                ->where(
                    'consentimiento_id',
                    $id
                )
                ->select(
                    'id',
                    'nombre_original',
                    'archivo',
                    'mime',
                    'tamano',
                    'tipo',
                    'created_at'
                )
                ->orderBy('id')
                ->get();

        /*
         * Historial.
         */
        $out['historial'] =
            DB::table(
                'consentimiento_historial'
            )
                ->where(
                    'consentimiento_id',
                    $id
                )
                ->orderByDesc('id')
                ->get();

        return $out;
    }

    /**
     * ============================================================
     * REEMPLAZAR MARCADORES
     * ============================================================
     */
    private function reemplazarMarcadores(
        ?string $texto,
        array $vars
    ): string {
        return preg_replace_callback(
            '/\{\{\s*(\w+)\s*\}\}/',
            fn($m) =>
                $vars[
                    strtolower($m[1])
                ] ?? $m[0],
            $texto ?? ''
        );
    }

    /**
     * ============================================================
     * DECODIFICAR FIRMA
     * ============================================================
     */
    private function decodificarImagen(
        string $dataUrl,
        int $maxBytes = 2 * 1024 * 1024
    ): array {

        if (
            !preg_match(
                '/^data:(image\/(?:png|jpeg));base64,(.+)$/s',
                $dataUrl,
                $m
            )
        ) {
            abort(
                422,
                'La firma debe ser una imagen PNG o JPEG en base64.'
            );
        }

        $binario = base64_decode(
            str_replace(
                ' ',
                '+',
                $m[2]
            ),
            true
        );

        if (
            $binario === false ||
            $binario === ''
        ) {
            abort(
                422,
                'La imagen de la firma no es válida.'
            );
        }

        if (
            strlen($binario) >
            $maxBytes
        ) {
            abort(
                422,
                'La imagen de la firma es demasiado grande (máximo 2 MB).'
            );
        }

        if (
            @getimagesizefromstring(
                $binario
            ) === false
        ) {
            abort(
                422,
                'La firma no es una imagen válida.'
            );
        }

        return [
            $binario,
            $m[1]
        ];
    }
}