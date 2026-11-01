<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Flujo común de permisos y vacaciones:
 *   pendiente → aprobado | rechazado | cancelado
 *   aprobado  → cancelado
 * Requiere el trait ScopesPorClinica en el controlador.
 */
trait ResuelveSolicitudes
{
    protected function resolverSolicitud(
        Request $request,
        Model $solicitud,
        string $tipoHistorial,
        string $descripcionAprobado
    ): JsonResponse {
        $this->asegurarClinica($request, $solicitud->empleado->clinica_id);

        $data = $request->validate([
            'estado'        => 'required|in:aprobado,rechazado,cancelado',
            'observaciones' => 'required_if:estado,rechazado|nullable|string|max:255',
        ], $this->mensajes(), $this->atributos());

        $permitidas = [
            'pendiente' => ['aprobado', 'rechazado', 'cancelado'],
            'aprobado'  => ['cancelado'],
        ];

        if (!in_array($data['estado'], $permitidas[$solicitud->estado] ?? [], true)) {
            return response()->json([
                'message' => "No se puede cambiar una solicitud {$solicitud->estado} a {$data['estado']}.",
            ], 422);
        }

        $usuarioId = $this->registradoPor($request);

        DB::transaction(function () use ($solicitud, $data, $usuarioId, $tipoHistorial, $descripcionAprobado) {
            $solicitud->update([
                'estado'        => $data['estado'],
                'observaciones' => $data['observaciones'] ?? null,
                'resuelto_por'  => $usuarioId,
                'resuelto_en'   => now(),
            ]);

            if ($data['estado'] === 'aprobado') {
                $solicitud->empleado->registrarHistorial($tipoHistorial, $descripcionAprobado, [
                    'fecha'          => $solicitud->fecha_inicio->toDateString(),
                    'registrado_por' => $usuarioId,
                ]);
            }
        });

        return response()->json(['message' => 'Solicitud ' . $data['estado'], 'solicitud' => $solicitud->fresh('empleado')]);
    }
}