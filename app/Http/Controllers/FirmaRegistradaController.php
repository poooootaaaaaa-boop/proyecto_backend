<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirmaRegistradaController extends Controller
{
    /**
     * Normalizar tipo de firma.
     */
    private function tipoNormalizado(string $tipo): string
    {
        $tipo = ucfirst(strtolower($tipo));

        abort_unless(
            in_array($tipo, ['Clinica', 'Doctor'], true),
            422,
            'El tipo debe ser Clinica o Doctor.'
        );

        return $tipo;
    }

    /**
     * ============================================================
     * LISTAR FIRMAS REGISTRADAS
     * ============================================================
     *
     * GET:
     *
     * /firmas-registradas
     *
     * /firmas-registradas?tipo=Doctor
     *
     * /firmas-registradas?tipo=Doctor&referencia_id=4
     */
    public function index(Request $request)
    {
        $query = DB::table(
            'firmas_registradas'
        )->select(
            'id',
            'tipo',
            'referencia_id',
            'mime',
            'updated_at'
        );

        if ($request->filled('tipo')) {

            $query->where(
                'tipo',
                $this->tipoNormalizado(
                    $request->input('tipo')
                )
            );
        }

        if ($request->filled('referencia_id')) {

            $query->where(
                'referencia_id',
                $request->input('referencia_id')
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * ============================================================
     * REGISTRAR FIRMA
     * ============================================================
     *
     * POST /firmas-registradas
     *
     * {
     *   "tipo": "Doctor",
     *   "referencia_id": 4,
     *   "firma": "data:image/png;base64,..."
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' =>
                'required|string',

            'referencia_id' =>
                'required|integer',

            'firma' =>
                'required|string',
        ]);

        $tipo =
            $this->tipoNormalizado(
                $data['tipo']
            );

        $tabla =
            $tipo === 'Clinica'
                ? 'clinicas'
                : 'doctores';

        abort_unless(
            DB::table($tabla)
                ->where(
                    'id',
                    $data['referencia_id']
                )
                ->exists(),
            404,
            'No se encontró el registro indicado.'
        );

        /*
         * Validar imagen.
         */
        if (
            !preg_match(
                '/^data:(image\/(?:png|jpeg));base64,(.+)$/s',
                $data['firma'],
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

        abort_if(
            $binario === false ||
            $binario === '' ||
            @getimagesizefromstring(
                $binario
            ) === false,
            422,
            'La imagen de la firma no es válida.'
        );

        abort_if(
            strlen($binario) >
            2 * 1024 * 1024,
            422,
            'La imagen de la firma es demasiado grande (máximo 2 MB).'
        );

        $now = now();

        /*
         * Crear o actualizar firma.
         */
        DB::table(
            'firmas_registradas'
        )->updateOrInsert(
            [
                'tipo' =>
                    $tipo,

                'referencia_id' =>
                    $data['referencia_id']
            ],
            [
                'firma' =>
                    $binario,

                'mime' =>
                    $m[1],

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Firma registrada.'
        ], 201);
    }

    /**
     * ============================================================
     * MOSTRAR IMAGEN
     * ============================================================
     *
     * GET:
     * /firmas-registradas/Doctor/4/imagen
     */
    public function imagen(
        $tipo,
        $referenciaId
    ) {
        $row = DB::table(
            'firmas_registradas'
        )
            ->where(
                'tipo',
                $this->tipoNormalizado($tipo)
            )
            ->where(
                'referencia_id',
                $referenciaId
            )
            ->first([
                'firma',
                'mime'
            ]);

        abort_if(
            !$row,
            404,
            'No hay firma registrada.'
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
     * ELIMINAR FIRMA
     * ============================================================
     *
     * DELETE:
     * /firmas-registradas/Doctor/4
     */
    public function destroy(
        $tipo,
        $referenciaId
    ) {
        $borradas = DB::table(
            'firmas_registradas'
        )
            ->where(
                'tipo',
                $this->tipoNormalizado($tipo)
            )
            ->where(
                'referencia_id',
                $referenciaId
            )
            ->delete();

        abort_if(
            !$borradas,
            404,
            'No hay firma registrada.'
        );

        return response()->json([
            'success' => true,
            'message' => 'Firma eliminada.'
        ]);
    }
}