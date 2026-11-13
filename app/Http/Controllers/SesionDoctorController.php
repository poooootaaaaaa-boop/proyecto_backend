<?php

namespace App\Http\Controllers;

use App\Models\SesionDoctor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SesionDoctorController extends Controller
{
    /**
     * Registrar el inicio de sesión de un doctor.
     * Se llama desde AuthController después de reconocer el rostro.
     */
    public static function registrarInicio(
        Usuario $usuario,
        $accessToken
    ): void {
        // Solo registrar doctores: rol_id = 3
        if ((int) $usuario->rol_id !== 3) {
            return;
        }

        SesionDoctor::create([
            'usuario_id' => $usuario->id,
            'token_id' => $accessToken->accessToken->id,
            'inicio_sesion' => now(),
            'fin_sesion' => null,
            'estado' => 'activa',
        ]);
    }

    /**
     * Historial de entradas y salidas.
     * GET /api/sesiones-doctores?desde=2026-11-01&hasta=2026-11-13&doctor_id=5
     */
    public function historial(Request $request)
    {
        $filtros = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
            'doctor_id' => 'nullable|integer|exists:doctores,id',
            'usuario_id' => 'nullable|integer|exists:usuarios,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $consulta = SesionDoctor::query()
            ->filtrar($filtros)
            ->with([
                'usuario:id,nombre,correo,foto_url',
                'doctor:id,usuario_id,especialidad_id',
                'doctor.especialidad:id,nombre',
            ]);

        $estadisticas = $this->estadisticas(clone $consulta);
        $pagina = $consulta
            ->orderByDesc('inicio_sesion')
            ->paginate((int) ($filtros['per_page'] ?? 50));

        return response()->json([
            'filtros' => [
                'desde' => $filtros['desde'] ?? null,
                'hasta' => $filtros['hasta'] ?? null,
                'doctor_id' => isset($filtros['doctor_id']) ? (int) $filtros['doctor_id'] : null,
                'usuario_id' => isset($filtros['usuario_id']) ? (int) $filtros['usuario_id'] : null,
            ],
            'estadisticas' => $estadisticas,
            'sesiones' => $pagina->getCollection()
                ->map(fn (SesionDoctor $sesion) => $this->presentar($sesion))
                ->values(),
            'meta' => [
                'current_page' => $pagina->currentPage(),
                'last_page' => $pagina->lastPage(),
                'per_page' => $pagina->perPage(),
                'total' => $pagina->total(),
            ],
        ]);
    }

    private function estadisticas($consulta): array
    {
        $promedio = DB::table('sesiones_doctores')
            ->whereIn('id', (clone $consulta)->select('sesiones_doctores.id'))
            ->whereNotNull('fin_sesion')
            ->avg(DB::raw('TIMESTAMPDIFF(SECOND, inicio_sesion, fin_sesion)'));

        $segundos = $promedio === null ? null : (int) round((float) $promedio);

        return [
            'total' => (clone $consulta)->count(),
            'activas' => (clone $consulta)->where('estado', 'activa')->count(),
            'cerradas' => (clone $consulta)->where('estado', 'cerrada')->count(),
            'duracion_promedio_segundos' => $segundos,
            'duracion_promedio' => $this->formatearDuracion($segundos),
        ];
    }

    private function presentar(SesionDoctor $sesion): array
    {
        $segundos = $sesion->fin_sesion
            ? abs((int) $sesion->inicio_sesion->diffInSeconds($sesion->fin_sesion))
            : null;

        return [
            'id' => $sesion->id,
            'estado' => $sesion->estado,
            'inicio_sesion' => $sesion->inicio_sesion?->toIso8601String(),
            'fin_sesion' => $sesion->fin_sesion?->toIso8601String(),
            'duracion_segundos' => $segundos,
            'duracion' => $this->formatearDuracion($segundos),
            'doctor' => [
                'id' => $sesion->doctor?->id,
                'usuario_id' => $sesion->usuario_id,
                'nombre' => $sesion->usuario?->nombre,
                'correo' => $sesion->usuario?->correo,
                'foto_url' => $sesion->usuario?->foto_url,
                'especialidad' => $sesion->doctor?->especialidad?->nombre,
            ],
        ];
    }

    private function formatearDuracion(?int $segundos): ?string
    {
        if ($segundos === null) {
            return null;
        }

        $horas = intdiv($segundos, 3600);
        $minutos = intdiv($segundos % 3600, 60);

        if ($horas > 0) {
            return $horas . ' h ' . $minutos . ' min';
        }

        return $minutos . ' min';
    }
}
